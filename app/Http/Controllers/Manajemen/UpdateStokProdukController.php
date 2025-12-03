<?php

namespace App\Http\Controllers\Manajemen;

use App\Models\UpdateStokProduk;
use App\Models\Produk;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UpdateStokProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Set timezone ke Asia/Jakarta untuk semua tanggal
            config(['app.timezone' => 'Asia/Jakarta']);
            date_default_timezone_set('Asia/Jakarta');
            
            // Ambil semua produk untuk dropdown
            $produk = Produk::orderBy('nama')->get();
            
            // Ambil history update stok dengan relasi produk
            $history = UpdateStokProduk::with('produk')
                ->orderBy('tanggal_update', 'desc')
                ->get();
            
            // Hitung summary dengan timezone Jakarta
            $now = Carbon::now('Asia/Jakarta');
            $totalEntries = $history->count();
            
            // Count today entries
            $todayEntries = $history->filter(function ($item) use ($now) {
                return Carbon::parse($item->tanggal_update)->isSameDay($now);
            })->count();
            
            // Count week entries
            $weekEntries = $history->filter(function ($item) use ($now) {
                return Carbon::parse($item->tanggal_update) >= $now->copy()->startOfWeek();
            })->count();
            
            // Count month entries
            $monthEntries = $history->filter(function ($item) use ($now) {
                return Carbon::parse($item->tanggal_update) >= $now->copy()->startOfMonth();
            })->count();
            
            return view("manajemen.produk.UpdateStokProduk", compact(
                'produk', 
                'history',
                'totalEntries',
                'todayEntries',
                'weekEntries',
                'monthEntries'
            ));
            
        } catch (\Exception $e) {
            Log::error('Index Update Stok Error: '.$e->getMessage());
            return back()->with('error', 'Gagal memuat data: '.$e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Store Update Stok Request:', $request->all());

        $request->validate([
            'id_produk' => 'required|exists:produk,id',
            'stok_baru' => 'required|integer|min:1',
            'kadaluarsa' => 'required|date|after_or_equal:today',
            'keterangan' => 'nullable|string|max:500'
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Ambil produk yang akan diupdate
                $produk = Produk::findOrFail($request->id_produk);
                
                // Simpan stok awal
                $stok_awal = $produk->stok;
                
                // Hitung total stok baru
                $total_stok = $stok_awal + $request->stok_baru;
                
                // Update produk (termasuk kadaluarsa)
                $produk->update([
                    'stok' => $total_stok,
                    'kadaluarsa' => $request->kadaluarsa,
                ]);
                
                // Simpan history update dengan waktu WIB (Asia/Jakarta)
                UpdateStokProduk::create([
                    'id_produk' => $request->id_produk,
                    'stok_awal' => $stok_awal,
                    'stok_baru' => $request->stok_baru,
                    'total_stok' => $total_stok,
                    'kadaluarsa' => $request->kadaluarsa,
                    'tanggal_update' => Carbon::now('Asia/Jakarta'),
                    'keterangan' => $request->keterangan
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil ditambahkan',
            ]);
        } catch (\Exception $e) {
            Log::error('Store Update Stok Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate stok: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $updateStok = UpdateStokProduk::with('produk')->findOrFail($id);

            // Format tanggal untuk Indonesia
            Carbon::setLocale('id');
            $tanggalUpdate = Carbon::parse($updateStok->tanggal_update)
                ->timezone('Asia/Jakarta')
                ->translatedFormat('l, d F Y H:i');
            
            $kadaluarsa = Carbon::parse($updateStok->kadaluarsa)
                ->translatedFormat('d F Y');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $updateStok->id,
                    'nama_produk' => $updateStok->produk->nama,
                    'stok_awal' => $updateStok->stok_awal,
                    'stok_baru' => $updateStok->stok_baru,
                    'total_stok' => $updateStok->total_stok,
                    'kadaluarsa' => $kadaluarsa,
                    'tanggal_update' => $tanggalUpdate,
                    'keterangan' => $updateStok->keterangan
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Show Update Stok Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Log::info('Update Stok History Request:', ['id' => $id, 'data' => $request->all()]);

        $request->validate([
            'stok_baru' => 'required|integer|min:1',
            'kadaluarsa' => 'required|date|after_or_equal:today',
            'keterangan' => 'nullable|string|max:500'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                // Ambil data history yang akan diupdate
                $updateStok = UpdateStokProduk::findOrFail($id);
                $produk = Produk::findOrFail($updateStok->id_produk);
                
                // Simpan nilai lama untuk perhitungan
                $stok_baru_lama = $updateStok->stok_baru;
                $stok_baru_baru = $request->stok_baru;
                
                // Hitung selisih stok
                $selisih_stok = $stok_baru_baru - $stok_baru_lama;
                
                // Update stok produk
                $produk->update([
                    'stok' => $produk->stok + $selisih_stok,
                    'kadaluarsa' => $request->kadaluarsa,
                ]);
                
                // Update history
                $updateStok->update([
                    'stok_baru' => $stok_baru_baru,
                    'total_stok' => $updateStok->stok_awal + $stok_baru_baru,
                    'kadaluarsa' => $request->kadaluarsa,
                    'keterangan' => $request->keterangan
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Data stok berhasil diupdate',
            ]);
        } catch (\Exception $e) {
            Log::error('Update Stok History Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Ambil data update stok
                $updateStok = UpdateStokProduk::with('produk')->findOrFail($id);
                
                // Ambil produk terkait
                $produk = $updateStok->produk;
                
                // Kembalikan stok produk ke semula
                $produk->update([
                    'stok' => $produk->stok - $updateStok->stok_baru
                ]);
                
                // Hapus history
                $updateStok->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'History stok berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Update Stok Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus history: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Batch delete multiple entries.
     */
    public function batchDelete(Request $request)
    {
        Log::info('Batch Delete Request:', $request->all());

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:update_stok_produk,id'
        ]);

        try {
            DB::transaction(function () use ($request) {
                $ids = $request->ids;
                
                foreach ($ids as $id) {
                    $updateStok = UpdateStokProduk::with('produk')->findOrFail($id);
                    $produk = $updateStok->produk;
                    
                    // Kembalikan stok produk
                    $produk->update([
                        'stok' => $produk->stok - $updateStok->stok_baru
                    ]);
                    
                    // Hapus history
                    $updateStok->delete();
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Semua history berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            Log::error('Batch Delete Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus history: '.$e->getMessage(),
            ], 500);
        }
    }

    // Method create dan edit (kosong karena menggunakan modal)
    public function create() {}
    public function edit($id) {}
}