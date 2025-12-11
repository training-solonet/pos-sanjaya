<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\UpdateStokProduk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Ambil semua produk dengan data terkait
            $produk = Produk::with(['updateStokHistory' => function ($query) {
                $query->orderBy('tanggal_update', 'desc');
            }])->get();

            // Cek untuk notifikasi stok rendah/habis
            $lowStockProducts = $produk->filter(function ($product) {
                return $product->getStockStatus() === 'rendah';
            });

            $outOfStockProducts = $produk->filter(function ($product) {
                return $product->getStockStatus() === 'habis';
            });

            // Cek untuk notifikasi kadaluarsa
            $expiringProducts = $produk->filter(function ($product) {
                return $product->getExpiryStatus() === 'mendekati';
            });

            $expiredProducts = $produk->filter(function ($product) {
                return $product->getExpiryStatus() === 'expired';
            });

            // Simpan data notifikasi di session untuk ditampilkan sekali
            if ($request->session()->has('notifications_shown')) {
                $request->session()->forget('notifications');
            } else {
                $notifications = [];

                if ($outOfStockProducts->count() > 0) {
                    $notifications[] = [
                        'type' => 'danger',
                        'title' => 'Stok Habis',
                        'message' => 'Ada '.$outOfStockProducts->count().' produk yang stoknya habis.',
                        'products' => $outOfStockProducts->pluck('nama')->toArray(),
                    ];
                }

                if ($lowStockProducts->count() > 0) {
                    $notifications[] = [
                        'type' => 'warning',
                        'title' => 'Stok Menipis',
                        'message' => 'Ada '.$lowStockProducts->count().' produk yang stoknya menipis (di bawah stok minimum).',
                        'products' => $lowStockProducts->pluck('nama')->toArray(),
                    ];
                }

                if ($expiredProducts->count() > 0) {
                    $notifications[] = [
                        'type' => 'danger',
                        'title' => 'Produk Kadaluarsa',
                        'message' => 'Ada '.$expiredProducts->count().' produk yang sudah kadaluarsa.',
                        'products' => $expiredProducts->pluck('nama')->toArray(),
                    ];
                }

                if ($expiringProducts->count() > 0) {
                    $notifications[] = [
                        'type' => 'warning',
                        'title' => 'Kadaluarsa Mendekati',
                        'message' => 'Ada '.$expiringProducts->count().' produk yang akan kadaluarsa dalam 7 hari ke depan.',
                        'products' => $expiringProducts->pluck('nama')->toArray(),
                    ];
                }

                if (! empty($notifications)) {
                    $request->session()->flash('notifications', $notifications);
                    $request->session()->put('notifications_shown', true);
                }
            }

            return view('manajemen.produk.index', compact('produk'));

        } catch (\Exception $e) {
            Log::error('Index Product Error: '.$e->getMessage());

            return back()->with('error', 'Gagal memuat data produk: '.$e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Store Product Request:', $request->all());

        $request->validate([
            'nama' => 'required|string|max:255|unique:produk,nama',
            'stok' => 'required|integer|min:0',
            'min_stok' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0',
            'kadaluarsa' => 'required|date|after_or_equal:today',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Cari bahan baku pertama yang tersedia
                $bahanBaku = BahanBaku::first();

                if (! $bahanBaku) {
                    // Buat bahan baku default jika tidak ada
                    $bahanBaku = BahanBaku::create([
                        'nama' => 'Bahan Baku Umum',
                        'stok' => 0,
                        'min_stok' => 0,
                        'kategori' => 'Bahan Utama',
                        'harga_satuan' => 0,
                        'id_konversi' => 1, // Pastikan ada konversi dengan id=1
                        'tglupdate' => now(),
                    ]);
                }

                // Create product dengan bahan baku yang valid
                $produk = Produk::create([
                    'nama' => $request->nama,
                    'id_bahan_baku' => $bahanBaku->id,
                    'stok' => $request->stok,
                    'min_stok' => $request->min_stok,
                    'harga' => $request->harga,
                    'kadaluarsa' => $request->kadaluarsa,
                ]);

                // Jika stok awal > 0, buat history stok
                if ($request->stok > 0) {
                    UpdateStokProduk::create([
                        'id_produk' => $produk->id,
                        'stok_awal' => 0,
                        'stok_baru' => $request->stok,
                        'total_stok' => $request->stok,
                        'kadaluarsa' => $request->kadaluarsa,
                        'tanggal_update' => now(),
                        'keterangan' => 'Stok awal produk baru',
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
            ]);
        } catch (\Exception $e) {
            Log::error('Store Product Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $produk = Produk::with(['updateStokHistory' => function ($query) {
                $query->orderBy('tanggal_update', 'desc');
            }])->findOrFail($id);

            return response()->json([
                'id' => $produk->id,
                'nama' => $produk->nama,
                'stok' => $produk->stok,
                'min_stok' => $produk->min_stok,
                'harga' => $produk->harga,
                'kadaluarsa' => $produk->kadaluarsa->format('Y-m-d'),
                'status_stok' => $produk->status_stok,
                'sisa_hari' => $produk->sisa_hari,
                'update_stok_history' => $produk->updateStokHistory->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'stok_awal' => $history->stok_awal,
                        'stok_baru' => $history->stok_baru,
                        'total_stok' => $history->total_stok,
                        'kadaluarsa' => Carbon::parse($history->kadaluarsa)->format('d F Y'),
                        'tanggal_update' => Carbon::parse($history->tanggal_update)->format('d F Y H:i'),
                        'keterangan' => $history->keterangan,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            Log::error('Show Product Error: '.$e->getMessage());

            return response()->json([
                'error' => 'Produk tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Log::info('Update Product Request:', ['id' => $id, 'data' => $request->all()]);

        $request->validate([
            'nama' => 'required|string|max:255|unique:produk,nama,'.$id,
            'stok' => 'required|integer|min:0',
            'min_stok' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0',
            'kadaluarsa' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $produk = Produk::findOrFail($id);

                // Simpan stok lama untuk perhitungan
                $stokLama = $produk->stok;
                $stokBaru = $request->stok;

                // Hitung selisih stok
                $selisihStok = $stokBaru - $stokLama;

                // Jika ada perubahan stok, buat history
                if ($selisihStok != 0) {
                    UpdateStokProduk::create([
                        'id_produk' => $id,
                        'stok_awal' => $stokLama,
                        'stok_baru' => $selisihStok,
                        'total_stok' => $stokBaru,
                        'kadaluarsa' => $request->kadaluarsa,
                        'tanggal_update' => now(),
                        'keterangan' => 'Update stok melalui edit produk',
                    ]);

                    // KURANGI STOK BAHAN BAKU jika stok BERTAMBAH (selisih positif) (Tambahan Code)
                    if ($selisihStok > 0) {
                        Log::info("=== UPDATE PRODUK: Stok '{$produk->nama}' bertambah +{$selisihStok} ===");
                        try {
                            $this->reduceBahanBakuFromResep($produk, $selisihStok);
                        } catch (\Exception $e) {
                            Log::error('Error reduce bahan baku dari update produk: '.$e->getMessage());
                            // Tidak throw agar update produk tetap berhasil
                        }
                    } else {
                        Log::info("=== UPDATE PRODUK: Stok '{$produk->nama}' berkurang {$selisihStok}, tidak ada pengurangan bahan baku ===");
                    }
                }

                // Update product - TIDAK mengupdate id_bahan_baku
                $produk->update([
                    'nama' => $request->nama,
                    'stok' => $request->stok,
                    'min_stok' => $request->min_stok,
                    'harga' => $request->harga,
                    'kadaluarsa' => $request->kadaluarsa,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diupdate',
            ]);
        } catch (\Exception $e) {
            Log::error('Update Product Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate produk: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $produk = Produk::findOrFail($id);

                // Hapus semua history stok terkait
                UpdateStokProduk::where('id_produk', $id)->delete();

                // Hapus produk
                $produk->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Product Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus produk: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tambah stok produk (untuk fitur tambah stok terpisah)
     */
    public function tambahStok(Request $request, string $id)
    {
        $request->validate([
            'tambah_stok' => 'required|integer|min:1',
            'kadaluarsa_baru' => 'nullable|date|after_or_equal:today',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            $produk = Produk::findOrFail($id);

            $stokBaru = $request->tambah_stok;
            $kadaluarsaBaru = $request->kadaluarsa_baru ?: $produk->kadaluarsa;

            // Update stok produk
            $produk->update([
                'stok' => $produk->stok + $stokBaru,
                'kadaluarsa' => $kadaluarsaBaru,
            ]);

            // Buat history stok
            UpdateStokProduk::create([
                'id_produk' => $id,
                'stok_awal' => $produk->stok - $stokBaru,
                'stok_baru' => $stokBaru,
                'total_stok' => $produk->stok,
                'kadaluarsa' => $kadaluarsaBaru,
                'tanggal_update' => now(),
                'keterangan' => $request->keterangan ?? 'Penambahan stok manual',
            ]);

            // KURANGI STOK BAHAN BAKU BERDASARKAN RESEP (Tambahan Code)
            Log::info("=== TAMBAH STOK PRODUK '{$produk->nama}' +{$stokBaru} ===");
            try {
                $this->reduceBahanBakuFromResep($produk, $stokBaru);
            } catch (\Exception $e) {
                Log::error('Error reduce bahan baku: '.$e->getMessage());
                // Tidak throw agar update stok produk tetap berhasil
            }

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil ditambahkan',
                'stok_baru' => $produk->stok,
            ]);
        } catch (\Exception $e) {
            Log::error('Tambah Stok Product Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah stok: '.$e->getMessage(),
            ], 500);
        }
    }

    // Method create dan edit (kosong karena menggunakan modal)
    public function create() {}

    public function edit(string $id) {}

    /**
     * Tambahan code
     * Kurangi stok bahan baku berdasarkan resep produk
     */
    private function reduceBahanBakuFromResep($produk, $jumlahProduk)
    {
        try {
            Log::info(">>> Produk '{$produk->nama}' +{$jumlahProduk}, cek resep...");

            // Cari resep aktif untuk produk ini
            $resep = DB::table('resep')
                ->where('id_produk', $produk->id)
                ->where('status', 'aktif')
                ->first();

            if (! $resep) {
                Log::warning("Produk '{$produk->nama}' tidak memiliki resep aktif");

                return;
            }

            Log::info("Resep: #{$resep->id} - {$resep->nama}");

            // Ambil rincian resep
            $rincianReseps = DB::table('rincian_resep')
                ->where('id_resep', $resep->id)
                ->get();

            foreach ($rincianReseps as $rincian) {
                $namaBahan = $rincian->nama_bahan;
                $qtyPer1 = (float) $rincian->qty;
                $unit = strtolower(trim($rincian->hitungan ?? 'gram'));

                $totalQty = $qtyPer1 * $jumlahProduk;

                Log::info("  → {$namaBahan}: {$qtyPer1} {$unit} × {$jumlahProduk} = {$totalQty} {$unit}");

                // Cari bahan
                $bahan = DB::table('bahan_baku')
                    ->join('konversi', 'bahan_baku.id_konversi', '=', 'konversi.id')
                    ->where('bahan_baku.nama', $namaBahan)
                    ->select('bahan_baku.*', 'konversi.satuan_kecil')
                    ->first();

                if (! $bahan) {
                    Log::warning("Bahan '{$namaBahan}' tidak ditemukan");

                    continue;
                }

                // GUNAKAN satuan_kecil dari database langsung (TIDAK dikonversi lagi!)
                $satuanStok = strtolower($bahan->satuan_kecil ?? 'gram');

                $convertedQty = $this->convertUnit($totalQty, $unit, $satuanStok);

                if ($convertedQty === null) {
                    Log::error("Konversi gagal: {$totalQty} {$unit} → {$satuanStok}");

                    continue;
                }

                if ($bahan->stok < $convertedQty) {
                    Log::error("Stok {$namaBahan} tidak cukup: {$bahan->stok} < {$convertedQty}");

                    continue;
                }

                $stokBaru = $bahan->stok - $convertedQty;
                DB::table('bahan_baku')
                    ->where('id', $bahan->id)
                    ->update([
                        'stok' => $stokBaru,
                        'tglupdate' => now(),
                        'updated_at' => now(),
                    ]);

                Log::info("  ✓ {$namaBahan} berkurang {$convertedQty} {$satuanStok}. Sisa: {$stokBaru}");
            }
        } catch (\Exception $e) {
            Log::error('ERROR reduceBahanBaku: '.$e->getMessage());
            throw $e;
        }
    }

    private function mapToSmallestUnit($satuan)
    {
        $map = ['kg' => 'gram', 'l' => 'ml', 'liter' => 'ml'];

        return $map[$satuan] ?? $satuan;
    }

    private function convertUnit($quantity, $fromUnit, $toUnit)
    {
        $fromUnit = strtolower(trim($fromUnit));
        $toUnit = strtolower(trim($toUnit));

        if ($fromUnit === $toUnit) {
            return $quantity;
        }

        $conversions = [
            'kg' => ['gram' => 1000, 'g' => 1000],
            'gram' => ['kg' => 0.001, 'g' => 1, 'slice' => 0.1, 'pcs' => 1],
            'g' => ['kg' => 0.001, 'gram' => 1, 'slice' => 0.1, 'pcs' => 1],
            'l' => ['ml' => 1000, 'liter' => 1],
            'liter' => ['ml' => 1000, 'l' => 1],
            'ml' => ['l' => 0.001, 'liter' => 0.001],
            'sdm' => ['ml' => 15, 'gram' => 15, 'g' => 15],
            'sdt' => ['ml' => 5, 'gram' => 5, 'g' => 5],
            'slice' => ['gram' => 10, 'g' => 10],
            'pcs' => ['gram' => 1, 'g' => 1],
        ];

        return isset($conversions[$fromUnit][$toUnit]) ? $quantity * $conversions[$fromUnit][$toUnit] : null;
    }
}
