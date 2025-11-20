<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BahanbakuController extends Controller
{
    public function index()
    {
        $bahan_baku = BahanBaku::all();
        return view("manajemen.bahanbaku.index", compact('bahan_baku'));
    }

    public function store(Request $request)
    {
        Log::info('Store method called', $request->all());
        
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'stok' => 'required|integer|min:0',
                'min_stok' => 'required|integer|min:0',
                'kategori' => 'required|in:Bahan Utama,Bahan Pembantu',
                'harga_satuan' => 'required|integer|min:0',
            ]);

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
            ]);

        } catch (\Exception $e) {
            Log::error('Store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $bahan_baku = BahanBaku::findOrFail($id);
            return response()->json($bahan_baku);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Bahan baku tidak ditemukan'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Update method called', ['id' => $id, 'data' => $request->all()]);
        
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'stok' => 'required|integer|min:0',
                'min_stok' => 'required|integer|min:0',
                'kategori' => 'required|in:Bahan Utama,Bahan Pembantu',
                'harga_satuan' => 'required|integer|min:0',
            ]);

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
            Log::error('Update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $bahan_baku = BahanBaku::findOrFail($id);
            $bahan_baku->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Bahan baku berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tambahStok(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'tambah_stok' => 'required|integer|min:1',
            ]);

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
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiBahanBaku()
    {
        $bahan_baku = BahanBaku::select('id', 'nama')->get();
        return response()->json($bahan_baku);
    }

    // Method kosong untuk create dan edit
    public function create() {}
    public function edit($id) {}
}