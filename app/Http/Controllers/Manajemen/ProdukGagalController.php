<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\DetailProdukGagal;
use App\Models\Produk;
use App\Models\ProdukGagal;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProdukGagalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');

            $query = ProdukGagal::with(['produk', 'detail.bahanBaku.konversi'])
                ->orderBy('tanggal_gagal', 'desc')
                ->orderBy('created_at', 'desc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_produk', 'LIKE', "%{$search}%")
                        ->orWhere('keterangan', 'LIKE', "%{$search}%")
                        ->orWhereHas('produk', function ($q2) use ($search) {
                            $q2->where('nama', 'LIKE', "%{$search}%");
                        });
                });
            }

            $produkGagal = $query->paginate(10);

            return view('manajemen.produkgagal.index', compact('produkGagal', 'search'));

        } catch (\Exception $e) {
            Log::error('ProdukGagal Index Error: '.$e->getMessage());

            return back()->with('error', 'Gagal memuat data produk gagal.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            // Dua fungsi dalam satu method:
            // 1. Jika ada parameter produk_id -> kembalikan resep
            // 2. Jika tidak ada parameter -> kembalikan daftar produk

            if ($request->has('produk_id')) {
                $produkId = $request->input('produk_id');

                // Cari produk
                $produk = Produk::find($produkId);
                if (! $produk) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Produk tidak ditemukan',
                    ], 404);
                }

                // Cari resep berdasarkan id_produk
                $resep = Resep::with(['rincianResep'])
                    ->where('id_produk', $produkId)
                    ->first();

                if (! $resep) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Produk yang dipilih tidak memiliki resep',
                        'produk_nama' => $produk->nama,
                    ]);
                }

                // Ambil bahan baku dari rincian resep
                $bahanResep = [];

                foreach ($resep->rincianResep as $rincian) {
                    // Cari bahan baku berdasarkan nama
                    $bahanBaku = BahanBaku::with('konversi')
                        ->where('nama', $rincian->nama_bahan)
                        ->first();

                    if ($bahanBaku) {
                        $bahanResep[] = [
                            'id' => $bahanBaku->id,
                            'nama' => $bahanBaku->nama,
                            'quantity_per_produk' => (float) $rincian->qty,
                            'unit' => $rincian->hitungan,
                            'harga_satuan' => $bahanBaku->harga_satuan,
                            'stok' => $bahanBaku->stok,
                            'satuan_kecil' => $bahanBaku->konversi ? $bahanBaku->konversi->satuan_kecil : 'gram',
                            'satuan_besar' => $bahanBaku->konversi ? $bahanBaku->konversi->satuan_besar : null,
                            'konversi' => $bahanBaku->konversi ? $bahanBaku->konversi->jumlah : 1,
                            'stok_display' => $bahanBaku->stok.' '.($bahanBaku->konversi ? $bahanBaku->konversi->satuan_kecil : 'gram'),
                        ];
                    } else {
                        // Jika bahan tidak ditemukan, buat entry default
                        Log::warning("Bahan baku '{$rincian->nama_bahan}' tidak ditemukan di database");
                        $bahanResep[] = [
                            'id' => 0,
                            'nama' => $rincian->nama_bahan,
                            'quantity_per_produk' => (float) $rincian->qty,
                            'unit' => $rincian->hitungan,
                            'harga_satuan' => 0,
                            'stok' => 0,
                            'satuan_kecil' => $rincian->hitungan,
                            'satuan_besar' => $rincian->hitungan,
                            'konversi' => 1,
                            'stok_display' => '0 '.$rincian->hitungan,
                            'warning' => true,
                        ];
                    }
                }

                if (empty($bahanResep)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resep ditemukan tetapi tidak ada bahan baku yang terdaftar',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'resep_id' => $resep->id,
                    'produk_nama' => $produk->nama,
                    'produk_id' => $produk->id,
                    'bahan_resep' => $bahanResep,
                ]);
            } else {
                // Ambil data produk untuk dropdown
                $produk = Produk::select('id', 'nama', 'sku')->orderBy('nama')->get();

                return response()->json([
                    'success' => true,
                    'produk' => $produk,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Create ProdukGagal Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data untuk form',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id',
            'jumlah_gagal' => 'required|integer|min:1',
            'tanggal_gagal' => 'required|date',
            'keterangan' => 'nullable|string',
            'bahan_baku' => 'required|array|min:1',
            'bahan_baku.*.id' => 'required',
            'bahan_baku.*.jumlah_digunakan' => 'required|numeric|min:0.001',
            'bahan_baku.*.unit' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Ambil data produk untuk nama produk
            $produk = Produk::findOrFail($request->produk_id);

            // Simpan produk gagal
            $produkGagal = ProdukGagal::create([
                'produk_id' => $request->produk_id,
                'nama_produk' => $produk->nama,
                'jumlah_gagal' => $request->jumlah_gagal,
                'tanggal_gagal' => $request->tanggal_gagal,
                'keterangan' => $request->keterangan,
                'created_by' => auth()->user()->name ?? 'System',
            ]);

            // Simpan detail bahan baku dan kurangi stok
            foreach ($request->bahan_baku as $bahan) {
                // Skip jika id bahan = 0 (bahan tidak ditemukan di database)
                if ($bahan['id'] == 0) {
                    Log::warning('Bahan baku dengan ID 0 dilewati: '.($bahan['nama'] ?? 'Unknown'));

                    continue;
                }

                $bahanBaku = BahanBaku::with('konversi')->find($bahan['id']);

                // Jika bahan tidak ditemukan, skip
                if (! $bahanBaku) {
                    Log::warning('Bahan baku dengan ID '.$bahan['id'].' tidak ditemukan');

                    continue;
                }

                // Konversi satuan ke satuan kecil untuk pengurangan stok
                $jumlahDalamSatuanKecil = $this->convertToSatuanKecil(
                    $bahan['jumlah_digunakan'],
                    $bahan['unit'],
                    $bahanBaku
                );

                // Validasi stok cukup
                if ($bahanBaku->stok < $jumlahDalamSatuanKecil) {
                    throw new \Exception("Stok bahan baku '{$bahanBaku->nama}' tidak cukup. Stok tersedia: {$bahanBaku->stok} {$bahanBaku->konversi->satuan_kecil}, dibutuhkan: {$jumlahDalamSatuanKecil} {$bahanBaku->konversi->satuan_kecil}");
                }

                // Tentukan jenis satuan
                $satuanType = $this->determineSatuanType($bahan['unit'], $bahanBaku);

                // Simpan detail
                DetailProdukGagal::create([
                    'produk_gagal_id' => $produkGagal->id,
                    'bahan_baku_id' => $bahan['id'],
                    'jumlah_digunakan' => $bahan['jumlah_digunakan'],
                    'satuan' => $satuanType,
                ]);

                // Kurangi stok bahan baku
                $bahanBaku->stok -= $jumlahDalamSatuanKecil;
                $bahanBaku->tglupdate = now();
                $bahanBaku->save();

                Log::info("Bahan baku '{$bahanBaku->nama}' berkurang {$jumlahDalamSatuanKecil} {$bahanBaku->konversi->satuan_kecil}. Sisa: {$bahanBaku->stok}");
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Produk gagal berhasil dicatat dan stok bahan baku telah dikurangi.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Produk Gagal Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan produk gagal: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Konversi ke satuan kecil
     */
    private function convertToSatuanKecil($jumlah, $unit, $bahanBaku)
    {
        $unit = strtolower(trim($unit));
        $satuanKecil = strtolower(trim($bahanBaku->konversi->satuan_kecil));

        // Jika satuan sudah sama dengan satuan kecil, tidak perlu konversi
        if ($unit === $satuanKecil) {
            return $jumlah;
        }

        // Konversi manual untuk satuan umum
        $conversions = [
            'kg' => ['g' => 1000, 'gram' => 1000],
            'g' => ['kg' => 0.001],
            'gram' => ['kg' => 0.001],
            'l' => ['ml' => 1000, 'liter' => 1],
            'liter' => ['ml' => 1000],
            'ml' => ['l' => 0.001, 'liter' => 0.001],
            'sdm' => ['ml' => 15, 'gram' => 15, 'g' => 15],
            'sdt' => ['ml' => 5, 'gram' => 5, 'g' => 5],
            'pcs' => ['pcs' => 1],
            'slice' => ['slice' => 1],
        ];

        if (isset($conversions[$unit][$satuanKecil])) {
            return $jumlah * $conversions[$unit][$satuanKecil];
        }

        // Default: asumsikan 1:1
        return $jumlah;
    }

    /**
     * Tentukan tipe satuan (besar/kecil)
     */
    private function determineSatuanType($unit, $bahanBaku)
    {
        $unit = strtolower(trim($unit));
        $satuanBesar = strtolower(trim($bahanBaku->konversi->satuan_besar ?? ''));

        if ($satuanBesar && $unit === $satuanBesar) {
            return 'besar';
        }

        return 'kecil';
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $produkGagal = ProdukGagal::with(['produk', 'detail.bahanBaku.konversi'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $produkGagal->id,
                    'produk_id' => $produkGagal->produk_id,
                    'nama_produk' => $produkGagal->nama_produk,
                    'jumlah_gagal' => $produkGagal->jumlah_gagal,
                    'keterangan' => $produkGagal->keterangan,
                    'tanggal_gagal' => $produkGagal->tanggal_gagal->format('Y-m-d'),
                    'formatted_tanggal' => $produkGagal->getFormattedTanggalAttribute(),
                    'created_by' => $produkGagal->created_by,
                    'created_at' => $produkGagal->created_at->format('d M Y H:i'),
                    'total_biaya' => number_format($produkGagal->getTotalBiayaBahanAttribute(), 0, ',', '.'),
                    'detail' => $produkGagal->detail->map(function ($detail) {
                        $detail->load('bahanBaku.konversi');

                        return [
                            'bahan_baku_id' => $detail->bahan_baku_id,
                            'nama_bahan' => $detail->bahanBaku->nama,
                            'jumlah_digunakan' => $detail->jumlah_digunakan,
                            'satuan' => $detail->satuan,
                            'satuan_kecil' => $detail->bahanBaku->konversi->satuan_kecil,
                            'satuan_besar' => $detail->bahanBaku->konversi->satuan_besar,
                            'jumlah_dalam_kecil' => $detail->getJumlahDalamSatuanKecilAttribute(),
                            'stok_sebelum' => ($detail->bahanBaku->stok + $detail->getJumlahDalamSatuanKecilAttribute()),
                            'stok_sesudah' => $detail->bahanBaku->stok,
                        ];
                    }),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Show Produk Gagal Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Produk gagal tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $produkGagal = ProdukGagal::with('detail.bahanBaku')->findOrFail($id);

            // Kembalikan stok bahan baku
            foreach ($produkGagal->detail as $detail) {
                $bahanBaku = $detail->bahanBaku;
                $jumlahDikembalikan = $detail->getJumlahDalamSatuanKecilAttribute();

                $bahanBaku->stok += $jumlahDikembalikan;
                $bahanBaku->tglupdate = now();
                $bahanBaku->save();

                Log::info("Bahan baku '{$bahanBaku->nama}' dikembalikan {$jumlahDikembalikan} {$bahanBaku->konversi->satuan_kecil}. Stok sekarang: {$bahanBaku->stok}");
            }

            // Hapus produk gagal
            $produkGagal->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Produk gagal berhasil dihapus dan stok bahan baku telah dikembalikan.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Produk Gagal Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk gagal: '.$e->getMessage(),
            ], 500);
        }
    }
}
