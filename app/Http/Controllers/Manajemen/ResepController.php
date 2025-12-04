<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Load resep with rincian bahan to pass to the view
        $resep = \App\Models\Resep::with('rincianResep.bahanBaku')->get();

        $recipes = $resep->map(function ($r) {
            $ingredients = $r->rincianResep->map(function ($ir) {
                $qty = (int) ($ir->qty ?? 0);
                $price = (int) ($ir->harga ?? 0);
                $subtotal = $qty * $price;

                return [
                    'name' => optional($ir->bahanBaku)->nama ?? ($ir->nama_bahan ?? ''),
                    'quantity' => $qty,
                    'unit' => $ir->hitungan ?? '',
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];
            })->toArray();

            $foodCost = array_sum(array_column($ingredients, 'subtotal'));

            return [
                'id' => $r->id,
                'name' => $r->nama,
                'category' => $r->kategori ?? '',
                'yield' => $r->porsi ?? 1,
                'duration' => $r->waktu_pembuatan ?? '',
                'foodCost' => $foodCost,
                'sellingPrice' => $r->harga_jual ?? null,
                'margin' => $r->margin ?? 0,
                'status' => $r->status ?? 'Draft',
                'ingredients' => $ingredients,
                'instructions' => $r->langkah ?? '',
                'notes' => $r->catatan ?? '',
            ];
        })->toArray();

        // also provide bahan baku list to view for ingredient selection
        $bahans = \App\Models\BahanBaku::select('id', 'nama', 'stok')->get()->toArray();
        // provide produk list so the recipe modal can autofill name & harga
        $produks = \App\Models\Produk::select('id', 'nama', 'harga')->get()->toArray();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'recipes' => $recipes, 'bahans' => $bahans, 'produks' => $produks], 200);
        }

        return view('manajemen.resep.index', compact('resep', 'recipes', 'bahans', 'produks'));
    }

    /**
     * Normalize category labels coming from the frontend to match DB enum values.
     */
    private function normalizeCategory($category)
    {
        if (! $category) {
            return null;
        }
        $cat = trim($category);

        // direct replacements for common variants
        $map = [
            'Kue & Dessert' => 'Kue dan Dessert',
            'Roti & Pastry' => 'Roti dan Pastry',
            'Makanan Utama' => 'Makanan',
            'Kue dan Dessert' => 'Kue dan Dessert',
            'Roti dan Pastry' => 'Roti dan Pastry',
            'Makanan' => 'Makanan',
            'Minuman' => 'Minuman',
            'Snack' => 'Snack',
        ];

        if (isset($map[$cat])) {
            return $map[$cat];
        }

        // fallback: replace & with 'dan' and trim
        $cat2 = str_replace('&', 'dan', $cat);
        $cat2 = preg_replace('/\s+/', ' ', $cat2);

        return $cat2;
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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'yield' => 'nullable|integer|min:1',
            'duration' => 'nullable|string|max:100',
            'sellingPrice' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:Aktif,Draft,Nonaktif',
            'instructions' => 'nullable|string',
            'notes' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
        ]);

        $ingredients = $request->input('ingredients', []);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            // normalize category to match DB enum values
            $category = $this->normalizeCategory($data['category'] ?? null);

            $resep = \App\Models\Resep::create([
                'nama' => $data['name'],
                'porsi' => $data['yield'] ?? 1,
                'kategori' => $category,
                'waktu_pembuatan' => $data['duration'] ?? null,
                'langkah' => $data['instructions'] ?? null,
                'catatan' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'Draft',
                'harga_jual' => isset($data['sellingPrice']) ? $data['sellingPrice'] : null,
            ]);

            $hasIdBahan = \Illuminate\Support\Facades\Schema::hasColumn('rincian_resep', 'id_bahan');

            foreach ($ingredients as $ing) {
                $name = trim($ing['name'] ?? '');
                $qty = isset($ing['quantity']) ? (int) $ing['quantity'] : 0;
                $unit = $ing['unit'] ?? null;
                $price = isset($ing['price']) ? (int) $ing['price'] : 0;

                $idBahan = null;
                if ($name) {
                    $bahan = \App\Models\BahanBaku::where('nama', $name)->first();
                    if (! $bahan) {
                        $bahan = \App\Models\BahanBaku::create([
                            'nama' => $name,
                            'stok' => 0,
                            'kategori' => null,
                            'min_stok' => 0,
                            'harga_satuan' => $price,
                            'tglupdate' => now(),
                        ]);
                    }
                    $idBahan = $bahan->id;
                }

                $r = new \App\Models\RincianResep;
                $r->id_resep = $resep->id;
                if ($hasIdBahan && $idBahan) {
                    $r->id_bahan = $idBahan;
                }
                // store the nama_bahan as provided (redundant but convenient)
                $r->nama_bahan = $name;
                $r->qty = $qty;
                $r->hitungan = $unit;
                $r->harga = $price;
                $r->save();
            }

            // commit first, then compute and save margin based on persisted rincian
            \Illuminate\Support\Facades\DB::commit();

            $resep->load('rincianResep');

            // compute food cost from persisted rincian
            $foodCost = $resep->rincianResep->reduce(function ($carry, $ir) {
                $qty = (int) ($ir->qty ?? 0);
                $price = (int) ($ir->harga ?? 0);

                return $carry + ($qty * $price);
            }, 0);

            $selling = (int) ($resep->harga_jual ?? 0);
            $marginPercent = 0;
            if ($selling > 0) {
                $marginPercent = (int) round((($selling - $foodCost) / $selling) * 100);
            }
            $resep->margin = $marginPercent;
            $resep->save();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'recipe' => $resep], 201);
            }

            return redirect()->back()->with('success', 'Resep berhasil dibuat.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $r = \App\Models\Resep::with('rincianResep.bahanBaku')->findOrFail($id);

        $ingredients = $r->rincianResep->map(function ($ir) {
            $qty = (int) ($ir->qty ?? 0);
            $price = (int) ($ir->harga ?? 0);

            return [
                'name' => optional($ir->bahanBaku)->nama ?? ($ir->nama_bahan ?? ''),
                'quantity' => $qty,
                'unit' => $ir->hitungan ?? '',
                'price' => $price,
                'subtotal' => $qty * $price,
            ];
        })->toArray();

        $foodCost = array_sum(array_column($ingredients, 'subtotal'));

        $recipe = [
            'id' => $r->id,
            'name' => $r->nama,
            'category' => $r->kategori,
            'yield' => $r->porsi,
            'duration' => $r->waktu_pembuatan ? ($r->waktu_pembuatan.' menit') : null,
            'foodCost' => $foodCost,
            'sellingPrice' => $r->harga_jual ?? 0,
            'margin' => $r->margin ?? 0,
            'status' => $r->status ?? '',
            'ingredients' => $ingredients,
            'instructions' => $r->langkah ?? null,
            'notes' => $r->catatan ?? null,
        ];

        return view('manajemen.resep.show', compact('recipe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'yield' => 'nullable|integer|min:1',
            'duration' => 'nullable|string|max:100',
            'sellingPrice' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:Aktif,Draft,Nonaktif',
            'instructions' => 'nullable|string',
            'notes' => 'nullable|string',
            'ingredients' => 'required|array|min:1',
        ]);

        $ingredients = $request->input('ingredients', []);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $resep = \App\Models\Resep::findOrFail($id);

            // normalize category to match DB enum values
            $category = $this->normalizeCategory($data['category'] ?? $resep->kategori);

            $resep->update([
                'nama' => $data['name'],
                'porsi' => $data['yield'] ?? $resep->porsi,
                'kategori' => $category,
                'waktu_pembuatan' => $data['duration'] ?? $resep->waktu_pembuatan,
                'langkah' => $data['instructions'] ?? $resep->langkah,
                'catatan' => $data['notes'] ?? $resep->catatan,
                'status' => $data['status'] ?? $resep->status,
                'harga_jual' => isset($data['sellingPrice']) ? $data['sellingPrice'] : $resep->harga_jual,
            ]);

            \App\Models\RincianResep::where('id_resep', $resep->id)->delete();

            $hasIdBahan = \Illuminate\Support\Facades\Schema::hasColumn('rincian_resep', 'id_bahan');

            foreach ($ingredients as $ing) {
                $name = trim($ing['name'] ?? '');
                $qty = isset($ing['quantity']) ? (int) $ing['quantity'] : 0;
                $unit = $ing['unit'] ?? null;
                $price = isset($ing['price']) ? (int) $ing['price'] : 0;

                $idBahan = null;
                if ($name) {
                    $bahan = \App\Models\BahanBaku::where('nama', $name)->first();
                    if (! $bahan) {
                        $bahan = \App\Models\BahanBaku::create([
                            'nama' => $name,
                            'stok' => 0,
                            'kategori' => null,
                            'min_stok' => 0,
                            'harga_satuan' => $price,
                            'tglupdate' => now(),
                        ]);
                    }
                    $idBahan = $bahan->id;
                }

                $r = new \App\Models\RincianResep;
                $r->id_resep = $resep->id;
                if ($hasIdBahan && $idBahan) {
                    $r->id_bahan = $idBahan;
                }
                // store the nama_bahan as provided (redundant but convenient)
                $r->nama_bahan = $name;
                $r->qty = $qty;
                $r->hitungan = $unit;
                $r->harga = $price;
                $r->save();
            }

            \Illuminate\Support\Facades\DB::commit();

            $resep->load('rincianResep');

            // compute food cost from persisted rincian
            $foodCost = $resep->rincianResep->reduce(function ($carry, $ir) {
                $qty = (int) ($ir->qty ?? 0);
                $price = (int) ($ir->harga ?? 0);

                return $carry + ($qty * $price);
            }, 0);

            $selling = (int) ($resep->harga_jual ?? 0);
            $marginPercent = 0;
            if ($selling > 0) {
                $marginPercent = (int) round((($selling - $foodCost) / $selling) * 100);
            }
            $resep->margin = $marginPercent;
            $resep->save();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'recipe' => $resep], 200);
            }

            return redirect()->back()->with('success', 'Resep berhasil diupdate.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $resep = \App\Models\Resep::findOrFail($id);

            \App\Models\RincianResep::where('id_resep', $resep->id)->delete();

            $resep->delete();

            \Illuminate\Support\Facades\DB::commit();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true], 200);
            }

            return redirect()->back()->with('success', 'Resep berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
