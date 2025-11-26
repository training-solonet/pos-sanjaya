<?php
namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produk = Produk::with('bahan_baku')->get();
        $bahan_baku = BahanBaku::select('id', 'nama')->get();

        return view('manajemen.produk.index', compact('produk', 'bahan_baku'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Store Product Request:', $request->all());

        $request->validate([
            'nama' => 'required|string|max:255',
            'bahan_baku_ids' => 'required|array',
            'bahan_baku_ids.*' => 'exists:bahan_baku,id',
            'stok' => 'required|integer|min:0',
            'min_stok' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0',
            'kadaluarsa' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Create product
                $produk = Produk::create([
                    'nama' => $request->nama,
                    'stok' => $request->stok,
                    'min_stok' => $request->min_stok,
                    'harga' => $request->harga,
                    'kadaluarsa' => $request->kadaluarsa,
                ]);

                // Attach multiple bahan baku
                $produk->bahan_baku()->attach($request->bahan_baku_ids);
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
            $produk = Produk::with('bahan_baku')->findOrFail($id);

            return response()->json([
                'id' => $produk->id,
                'nama' => $produk->nama,
                'stok' => $produk->stok,
                'min_stok' => $produk->min_stok,
                'harga' => $produk->harga,
                'kadaluarsa' => $produk->kadaluarsa,
                'bahan_baku_ids' => $produk->bahan_baku->pluck('id')->toArray(),
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
            'bahan_baku_ids' => 'required|array',
            'bahan_baku_ids.*' => 'exists:bahan_baku,id',
            'stok' => 'required|integer|min:0',
            'min_stok' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0',
            'kadaluarsa' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $produk = Produk::findOrFail($id);

                // Update product
                $produk->update([
                    'nama' => $request->nama,
                    'stok' => $request->stok,
                    'min_stok' => $request->min_stok,
                    'harga' => $request->harga,
                    'kadaluarsa' => $request->kadaluarsa,
                ]);

                // Sync multiple bahan baku
                $produk->bahan_baku()->sync($request->bahan_baku_ids);
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
            $produk = Produk::findOrFail($id);
            $produk->delete();

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
