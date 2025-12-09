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

            // Jika request AJAX, kembalikan JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $opname_data,
                    'summary' => $summary,
                    'categories' => $categories,
                ]);
            }

            // Jika request riwayat
            if ($request->has('history') && $request->ajax()) {
                $histories = Opname::with('bahanBaku.konversi')
                    ->orderBy('tgl', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(function ($opname) {
                        $selisih = (float) $opname->stok - (float) $opname->bahanBaku->stok;

                        return [
                            'id' => $opname->id,
                            'nama_bahan' => $opname->bahanBaku->nama,
                            'stok_sistem' => (float) $opname->bahanBaku->stok,
                            'stok_fisik' => (float) $opname->stok,
                            'selisih' => $selisih,
                            'catatan' => $opname->catatan,
                            'tgl' => $opname->tgl->format('d M Y H:i'),
                            'satuan' => $opname->bahanBaku->konversi ?
                                $opname->bahanBaku->konversi->satuan_kecil : 'unit',
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'data' => $histories,
                ]);
            }

            return view('manajemen.bahanbaku.opname', compact('opname_data', 'summary', 'categories'));

        } catch (\Exception $e) {
            Log::error('Opname Index Error: '.$e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal memuat data opname');
        }
    }

    public function store(Request $request)
    {
        try {
            // Handle start new session
            if ($request->has('action') && $request->action === 'start_new_session') {
                return $this->startNewSession();
            }

            // Validate untuk save stok fisik
            $request->validate([
                'id_bahan' => 'required|exists:bahan_baku,id',
                'stok_fisik' => 'required|numeric|min:0',
                'catatan' => 'nullable|string|max:500',
            ]);

            $today = Carbon::today()->toDateString();

            // Create or update opname
            $opname = Opname::updateOrCreate(
                [
                    'id_bahan' => $request->id_bahan,
                    'tgl' => $today,
                ],
                [
                    'stok' => $request->stok_fisik,
                    'catatan' => $request->catatan,
                ]
            );

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

    private function startNewSession()
    {
        try {
            $today = Carbon::today()->toDateString();

            // Hapus semua opname hari ini
            $deleted = Opname::whereDate('tgl', $today)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesi opname baru telah dimulai',
                'deleted_count' => $deleted,
                'session_date' => Carbon::parse($today)->format('d M Y'),
            ]);

        } catch (\Exception $e) {
            Log::error('Start New Session Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: '.$e->getMessage(),
            ], 500);
        }
    }

    // Method lainnya tetap ada untuk route resource
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
