<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Produk;
use App\Models\UpdateStokProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Ambil semua produk dengan data terkait
            $produk = Produk::with(['updateStokHistory' => function($query) {
                $query->orderBy('tanggal_update', 'desc');
            }])->get();
            
            // Cek untuk notifikasi stok rendah/habis
            $lowStockProducts = $produk->filter(function($product) {
                return $product->getStockStatus() === 'rendah';
            });
            
            $outOfStockProducts = $produk->filter(function($product) {
                return $product->getStockStatus() === 'habis';
            });
            
            // Cek untuk notifikasi kadaluarsa
            $expiringProducts = $produk->filter(function($product) {
                return $product->getExpiryStatus() === 'mendekati';
            });
            
            $expiredProducts = $produk->filter(function($product) {
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
                        'message' => 'Ada ' . $outOfStockProducts->count() . ' produk yang stoknya habis.',
                        'products' => $outOfStockProducts->pluck('nama')->toArray()
                    ];
                }
                
                if ($lowStockProducts->count() > 0) {
                    $notifications[] = [
                        'type' => 'warning',
                        'title' => 'Stok Menipis',
                        'message' => 'Ada ' . $lowStockProducts->count() . ' produk yang stoknya menipis (di bawah stok minimum).',
                        'products' => $lowStockProducts->pluck('nama')->toArray()
                    ];
                }
                
                if ($expiredProducts->count() > 0) {
                    $notifications[] = [
                        'type' => 'danger',
                        'title' => 'Produk Kadaluarsa',
                        'message' => 'Ada ' . $expiredProducts->count() . ' produk yang sudah kadaluarsa.',
                        'products' => $expiredProducts->pluck('nama')->toArray()
                    ];
                }
                
                if ($expiringProducts->count() > 0) {
                    $notifications[] = [
                        'type' => 'warning',
                        'title' => 'Kadaluarsa Mendekati',
                        'message' => 'Ada ' . $expiringProducts->count() . ' produk yang akan kadaluarsa dalam 7 hari ke depan.',
                        'products' => $expiringProducts->pluck('nama')->toArray()
                    ];
                }
                
                if (!empty($notifications)) {
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
            'nama' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'min_stok' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0',
            'kadaluarsa' => 'required|date|after_or_equal:today',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Create product
                $produk = Produk::create([
                    'nama' => $request->nama,
                    'id_bahan_baku' => 1, // Default bahan baku
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
                        'keterangan' => 'Stok awal produk baru'
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
            $produk = Produk::with(['updateStokHistory' => function($query) {
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
                'update_stok_history' => $produk->updateStokHistory->map(function($history) {
                    return [
                        'id' => $history->id,
                        'stok_awal' => $history->stok_awal,
                        'stok_baru' => $history->stok_baru,
                        'total_stok' => $history->total_stok,
                        'kadaluarsa' => Carbon::parse($history->kadaluarsa)->format('d F Y'),
                        'tanggal_update' => Carbon::parse($history->tanggal_update)->format('d F Y H:i'),
                        'keterangan' => $history->keterangan
                    ];
                })
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
            'nama' => 'required|string|max:255',
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
                        'keterangan' => 'Update stok melalui edit produk'
                    ]);
                }

                // Update product
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

    // Method create dan edit (kosong karena menggunakan modal)
    public function create() {}
    public function edit(string $id) {}
}