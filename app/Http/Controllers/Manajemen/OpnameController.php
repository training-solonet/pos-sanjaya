<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Opname;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OpnameController extends Controller
{
    public function index(Request $request)
    {
        try {
            $today = Carbon::today()->toDateString();

            // Cek apakah sudah ada opname hari ini
            $has_opname_today = Opname::whereDate('tgl', $today)->exists();

            // Ambil semua bahan baku
            $bahan_baku = BahanBaku::with(['konversi'])->orderBy('nama')->get();

            $opname_data = [];
            $categories = [];

            foreach ($bahan_baku as $bahan) {
                // Ambil opname hari ini untuk bahan ini
                $opname_today = Opname::where('id_bahan', $bahan->id)
                    ->whereDate('tgl', $today)
                    ->first();

                $status = 'pending';
                $stok_fisik = null;
                $selisih = null;
                $tgl_opname_terakhir = null;
                $catatan_terakhir = null;

                if ($opname_today) {
                    $stok_fisik = (float) $opname_today->stok;
                    $selisih = $stok_fisik - (float) $bahan->stok;
                    $tgl_opname_terakhir = $opname_today->tgl;
                    $catatan_terakhir = $opname_today->catatan;

                    // Tentukan status
                    $status = abs($selisih) <= 0.01 ? 'counted' : 'discrepancy';
                }

                $satuan = $bahan->konversi ? $bahan->konversi->satuan_kecil : 'unit';
                $kategori = $bahan->kategori ?: 'Belum Dikategorikan';

                if (! in_array($kategori, $categories)) {
                    $categories[] = $kategori;
                }

                $opname_data[] = [
                    'id' => $bahan->id,
                    'nama' => $bahan->nama,
                    'kode' => 'BB-'.str_pad($bahan->id, 3, '0', STR_PAD_LEFT),
                    'kategori' => $kategori,
                    'stok_sistem' => (float) $bahan->stok,
                    'stok_fisik' => $stok_fisik,
                    'selisih' => $selisih,
                    'status' => $status,
                    'satuan' => $satuan,
                    'catatan' => $catatan_terakhir,
                    'tgl_opname_terakhir' => $tgl_opname_terakhir,
                ];
            }

            // Hitung statistik
            $total_bahan = count($opname_data);
            $dihitung = collect($opname_data)->whereIn('status', ['counted', 'discrepancy'])->count();
            $selisih_count = collect($opname_data)->where('status', 'discrepancy')->count();
            $progress = $total_bahan > 0 ? round(($dihitung / $total_bahan) * 100) : 0;

            $summary = [
                'total_bahan' => $total_bahan,
                'dihitung' => $dihitung,
                'selisih' => $selisih_count,
                'progress' => $progress,
            ];

            // Jika request riwayat
            if ($request->has('history')) {
                $startDate = $request->get('start_date', Carbon::today()->subDays(7)->format('Y-m-d'));
                $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));

                // Query untuk mengambil data riwayat dengan format yang benar
                $histories = Opname::with(['bahanBaku.konversi'])
                    ->whereDate('tgl', '>=', $startDate)
                    ->whereDate('tgl', '<=', $endDate)
                    ->orderBy('tgl', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function ($opname) {
                        $bahan = $opname->bahanBaku;
                        if (! $bahan) {
                            return null;
                        }

                        $selisih = (float) $opname->stok - (float) $bahan->stok;
                        $satuan = $bahan->konversi ? $bahan->konversi->satuan_kecil : 'unit';

                        return [
                            'id' => $opname->id,
                            'id_bahan' => $opname->id_bahan,
                            'nama_bahan' => $bahan->nama,
                            'kode' => 'BB-'.str_pad($bahan->id, 3, '0', STR_PAD_LEFT),
                            'kategori' => $bahan->kategori,
                            'stok_sistem' => (float) $bahan->stok,
                            'stok_fisik' => (float) $opname->stok,
                            'selisih' => $selisih,
                            'catatan' => $opname->catatan,
                            'tgl' => Carbon::parse($opname->tgl)->format('Y-m-d'),
                            'tgl_formatted' => Carbon::parse($opname->tgl)->translatedFormat('l, d F Y'),
                            'waktu' => Carbon::parse($opname->created_at)->format('H:i'),
                            'satuan' => $satuan,
                        ];
                    })
                    ->filter() // Hapus null jika ada bahan yang tidak ditemukan
                    ->values(); // Reset array keys

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'data' => $histories,
                        'message' => 'Data riwayat berhasil diambil',
                        'total_records' => $histories->count(),
                    ]);
                }
            }

            // Jika request AJAX untuk data opname
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $opname_data,
                    'summary' => $summary,
                    'categories' => $categories,
                    'has_opname_today' => $has_opname_today,
                ]);
            }

            return view('manajemen.bahanbaku.opname', compact(
                'opname_data',
                'summary',
                'categories',
                'has_opname_today'
            ));

        } catch (\Exception $e) {
            Log::error('Opname Index Error: '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: '.$e->getMessage(),
                    'error_details' => $e->getFile().':'.$e->getLine(),
                ], 500);
            }

            return back()->with('error', 'Gagal memuat data opname: '.$e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $today = Carbon::today()->toDateString();

            // Handle start new session
            if ($request->has('action') && $request->action === 'start_new_session') {
                return $this->startNewSession($today);
            }

            // Validate untuk save stok fisik
            $request->validate([
                'id_bahan' => 'required|exists:bahan_baku,id',
                'stok_fisik' => 'required|numeric|min:0',
                'catatan' => 'nullable|string|max:500',
            ]);

            // Cek apakah sudah ada opname hari ini untuk bahan ini
            $existing_opname = Opname::where('id_bahan', $request->id_bahan)
                ->whereDate('tgl', $today)
                ->first();

            if ($existing_opname) {
                // Update existing
                $existing_opname->update([
                    'stok' => $request->stok_fisik,
                    'catatan' => $request->catatan,
                    'updated_at' => Carbon::now(),
                ]);
                $opname = $existing_opname;
            } else {
                // Create new - INI YANG MENYIMPAN RIWAYAT
                $opname = Opname::create([
                    'id_bahan' => $request->id_bahan,
                    'tgl' => $today,
                    'stok' => $request->stok_fisik,
                    'catatan' => $request->catatan,
                ]);

                Log::info('New opname record created:', [
                    'id_bahan' => $request->id_bahan,
                    'stok' => $request->stok_fisik,
                    'catatan' => $request->catatan,
                    'tgl' => $today,
                ]);
            }

            // Ambil data bahan baku untuk response
            $bahan_baku = BahanBaku::with('konversi')->find($request->id_bahan);
            $selisih = (float) $request->stok_fisik - (float) $bahan_baku->stok;
            $status = abs($selisih) <= 0.01 ? 'counted' : 'discrepancy';

            return response()->json([
                'success' => true,
                'message' => 'Stok fisik berhasil disimpan',
                'data' => [
                    'stok_fisik' => (float) $request->stok_fisik,
                    'selisih' => $selisih,
                    'status' => $status,
                    'catatan' => $request->catatan,
                    'tgl' => Carbon::parse($today)->format('d M Y'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Store Opname Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    private function startNewSession($today)
    {
        try {
            // Cek apakah sudah ada opname hari ini
            $has_opname_today = Opname::whereDate('tgl', $today)->exists();

            if ($has_opname_today) {
                return response()->json([
                    'success' => false,
                    'message' => 'Opname hari ini sudah dilakukan. Anda hanya dapat melakukan opname sekali per hari.',
                ], 400);
            }

            // Hapus semua opname hari ini (seharusnya tidak ada karena baru dicek)
            $deleted = Opname::whereDate('tgl', $today)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesi opname baru telah dimulai',
                'deleted_count' => $deleted,
                'session_date' => Carbon::parse($today)->translatedFormat('d F Y'),
            ]);

        } catch (\Exception $e) {
            Log::error('Start New Session Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show(Opname $opname)
    {
        return response()->json(['success' => false, 'message' => 'Method tidak tersedia']);
    }

    public function update(Request $request, Opname $opname)
    {
        return response()->json(['success' => false, 'message' => 'Method tidak tersedia']);
    }

    public function destroy(Opname $opname)
    {
        return response()->json(['success' => false, 'message' => 'Method tidak tersedia']);
    }

    public function create()
    {
        abort(404);
    }

    public function edit(Opname $opname)
    {
        abort(404);
    }
}
