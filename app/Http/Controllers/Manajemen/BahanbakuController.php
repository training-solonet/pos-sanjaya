<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanbakuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bahan_baku = BahanBaku::all();
        return view("manajemen.bahanbaku.index", compact('bahan_baku'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:bahan_baku,nama',
            'stok' => 'required|integer|min:0',
            'min_stok' => 'required|integer|min:0',
            'kategori' => 'required|in:Bahan Utama,Bahan Pembantu',
            'harga_satuan' => 'required|integer|min:0',
        ]);

        try {
            // Create dengan fillable
            BahanBaku::create([
                'nama' => $validated['nama'],
                'stok' => $validated['stok'],
                'min_stok' => $validated['min_stok'],
                'kategori' => $validated['kategori'],
                'harga_satuan' => $validated['harga_satuan'],
                'tglupdate' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bahan baku berhasil ditambahkan'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan bahan baku: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $bahan_baku = BahanBaku::findOrFail($id);
            return response()->json($bahan_baku);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Bahan baku tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:bahan_baku,nama,' . $id,
            'stok' => 'required|integer|min:0',
            'min_stok' => 'required|integer|min:0',
            'kategori' => 'required|in:Bahan Utama,Bahan Pembantu',
            'harga_satuan' => 'required|integer|min:0',
        ]);

        try {
            $bahan_baku = BahanBaku::findOrFail($id);
            
            $bahan_baku->update([
                'nama' => $validated['nama'],
                'stok' => $validated['stok'],
                'min_stok' => $validated['min_stok'],
                'kategori' => $validated['kategori'],
                'harga_satuan' => $validated['harga_satuan'],
                'tglupdate' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bahan baku berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate bahan baku: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $bahan_baku = BahanBaku::findOrFail($id);
            
            // Cek apakah bahan baku digunakan di produk
            if ($bahan_baku->produk()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus bahan baku karena masih digunakan dalam produk'
                ], 400);
            }
            
            $bahan_baku->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bahan baku berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus bahan baku: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API untuk mendapatkan data bahan baku
     */
    public function apiBahanBaku()
    {
        try {
            $bahan_baku = BahanBaku::select('id', 'nama')->get();
            return response()->json($bahan_baku);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil data bahan baku'
            ], 500);
        }
    }

    /**
     * Tambah stok bahan baku
     */
    public function tambahStok(Request $request, $id)
    {
        $validated = $request->validate([
            'tambah_stok' => 'required|integer|min:1',
        ]);

        try {
            $bahan_baku = BahanBaku::findOrFail($id);
            
            $bahan_baku->update([
                'stok' => $bahan_baku->stok + $validated['tambah_stok'],
                'tglupdate' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah stok: ' . $e->getMessage()
            ], 500);
        }
    }
}