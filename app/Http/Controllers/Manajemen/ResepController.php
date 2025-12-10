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
                'status' => ucfirst($r->status ?? 'draft'), // Capitalize first letter untuk display
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
     * Konversi satuan menggunakan konversi manual
     * Stok bahan baku disimpan dalam satuan_kecil dari tabel konversi (gram, kg, pcs, ml, dll)
     */
    private function convertUnit($quantity, $fromUnit, $toUnit)
    {
        $fromUnit = strtolower(trim($fromUnit));
        $toUnit = strtolower(trim($toUnit));

        // Jika satuan sama, tidak perlu konversi
        if ($fromUnit === $toUnit) {
            return $quantity;
        }

        // Konversi manual untuk satuan umum
        $conversions = [
            // Berat
            'kg' => ['gram' => 1000, 'g' => 1000],
            'gram' => ['kg' => 0.001, 'g' => 1],
            'g' => ['kg' => 0.001, 'gram' => 1],

            // Volume
            'l' => ['ml' => 1000, 'liter' => 1],
            'liter' => ['ml' => 1000, 'l' => 1],
            'ml' => ['l' => 0.001, 'liter' => 0.001],

            // Sendok
            'sdm' => ['ml' => 15, 'gram' => 15, 'g' => 15, 'sdt' => 3],
            'sdt' => ['ml' => 5, 'gram' => 5, 'g' => 5, 'sdm' => 0.333],
        ];

        if (isset($conversions[$fromUnit][$toUnit])) {
            return $quantity * $conversions[$fromUnit][$toUnit];
        }

        return null;
    }

    /**
     * Mendapatkan satuan penyimpanan stok (selalu dalam satuan terkecil)
     * Untuk menghindari desimal pada kolom integer, stok disimpan dalam satuan terkecil:
     * - kg → disimpan dalam gram
     * - liter → disimpan dalam ml
     * - pcs, slice, dll → disimpan langsung
     */
    private function getBahanSatuan($bahan)
    {
        if ($bahan->konversi) {
            $satuanKecil = strtolower($bahan->konversi->satuan_kecil ?? 'gram');

            // Konversi ke satuan terkecil untuk menghindari desimal
            $mapToSmallest = [
                'kg' => 'gram',
                'l' => 'ml',
                'liter' => 'ml',
            ];

            return $mapToSmallest[$satuanKecil] ?? $satuanKecil;
        }

        return 'gram';
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
        \Illuminate\Support\Facades\Log::info('=== STORE RESEP START ===', ['request' => $request->all()]);

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

            // Cari atau buat produk berdasarkan nama resep
            $produk = \App\Models\Produk::where('nama', $data['name'])->first();
            if (! $produk) {
                $produk = \App\Models\Produk::create([
                    'nama' => $data['name'],
                    'harga' => $data['sellingPrice'] ?? 0,
                    'stok' => 0,
                    'kategori' => $category,
                    'tglupdate' => now(),
                ]);
            }

            // Ambil bahan pertama dari ingredients sebagai id_bahan_baku
            $firstIngredient = $ingredients[0] ?? null;
            $idBahanBaku = 1; // default

            if ($firstIngredient && isset($firstIngredient['name'])) {
                $bahanBaku = \App\Models\BahanBaku::where('nama', $firstIngredient['name'])->first();
                if ($bahanBaku) {
                    $idBahanBaku = $bahanBaku->id;
                }
            }

            $resep = \App\Models\Resep::create([
                'id_produk' => $produk->id,
                'id_bahan_baku' => $idBahanBaku,
                'nama' => $data['name'],
                'porsi' => $data['yield'] ?? 1,
                'kategori' => $category,
                'waktu_pembuatan' => $data['duration'] ?? null,
                'langkah' => $data['instructions'] ?? null,
                'catatan' => $data['notes'] ?? null,
                'status' => strtolower($data['status'] ?? 'draft'),
                'harga_jual' => isset($data['sellingPrice']) ? $data['sellingPrice'] : 0,
            ]);

            $hasIdBahan = \Illuminate\Support\Facades\Schema::hasColumn('rincian_resep', 'id_bahan');
            $stokErrors = [];
            // STOK BAHAN BAKU DIKURANGI SAAT TRANSAKSI, BUKAN SAAT BUAT RESEP
            $reduceStock = false;

            \Illuminate\Support\Facades\Log::info("CREATE RESEP '{$resep->nama}': Status = '{$resep->status}', ReduceStock = FALSE (stok dikurangi saat transaksi)");

            foreach ($ingredients as $ing) {
                $name = trim($ing['name'] ?? '');
                $qty = isset($ing['quantity']) ? (float) $ing['quantity'] : 0;
                $unit = ! empty($ing['unit']) ? trim($ing['unit']) : null;
                $price = isset($ing['price']) ? (int) $ing['price'] : 0;

                // Validasi unit tidak boleh kosong
                if (empty($unit)) {
                    $stokErrors[] = "Satuan untuk bahan '{$name}' tidak boleh kosong. Pilih satuan (gram, kg, pcs, dll).";

                    continue;
                }

                $idBahan = null;
                $bahan = null;

                if ($name) {
                    $bahan = \App\Models\BahanBaku::with('konversi')->where('nama', $name)->first();
                    if (! $bahan) {
                        // Cari konversi berdasarkan satuan_kecil yang sesuai dengan unit resep
                        $unitNormalized = strtolower(trim($unit ?? 'gram'));

                        $konversiDefault = \App\Models\Konversi::where('satuan_kecil', $unitNormalized)->first();

                        // Jika tidak ada, cari konversi default gram atau yang pertama
                        if (! $konversiDefault) {
                            $konversiDefault = \App\Models\Konversi::where('satuan_kecil', 'gram')
                                ->orWhere('satuan_kecil', 'kg')
                                ->orWhere('satuan_kecil', 'pcs')
                                ->first();
                        }

                        $bahan = \App\Models\BahanBaku::create([
                            'nama' => $name,
                            'stok' => 0,
                            'kategori' => null,
                            'min_stok' => 0,
                            'harga_satuan' => $price,
                            'id_konversi' => $konversiDefault ? $konversiDefault->id : 1,
                            'tglupdate' => now(),
                        ]);
                        $bahan->load('konversi');
                    }
                    $idBahan = $bahan->id;

                    // Kurangi stok jika status bukan draft
                    if ($reduceStock && $qty > 0) {
                        $resepSatuan = strtolower(trim($unit ?? 'gram'));
                        $bahanSatuan = $this->getBahanSatuan($bahan);

                        \Illuminate\Support\Facades\Log::info("DEBUG: Bahan '{$name}' - Stok: {$bahan->stok} {$bahanSatuan}, Resep butuh: {$qty} {$resepSatuan}");

                        // Konversi qty resep ke satuan stok bahan
                        $convertedQty = $this->convertUnit($qty, $resepSatuan, $bahanSatuan);

                        \Illuminate\Support\Facades\Log::info("DEBUG: Hasil konversi {$qty} {$resepSatuan} → {$bahanSatuan} = ".($convertedQty === null ? 'NULL' : $convertedQty));

                        if ($convertedQty === null) {
                            $stokErrors[] = "Tidak dapat mengkonversi {$qty} {$resepSatuan} ke {$bahanSatuan} untuk '{$name}'. Tambahkan konversi di menu Konversi Satuan.";
                        } elseif ($bahan->stok < $convertedQty) {
                            $stokErrors[] = "Stok '{$name}' tidak cukup. Tersedia: {$bahan->stok} {$bahanSatuan}, Dibutuhkan: ".number_format($convertedQty, 3)." {$bahanSatuan} (dari {$qty} {$resepSatuan})";
                        } else {
                            // Kurangi stok
                            $bahan->stok -= $convertedQty;
                            $bahan->tglupdate = now();
                            $bahan->save();

                            \Illuminate\Support\Facades\Log::info("Resep '{$resep->nama}': Stok '{$name}' berkurang {$qty} {$resepSatuan} = {$convertedQty} {$bahanSatuan}. Sisa: {$bahan->stok} {$bahanSatuan}");
                        }
                    }
                }

                $r = new \App\Models\RincianResep;
                $r->id_resep = $resep->id;
                if ($hasIdBahan && $idBahan) {
                    $r->id_bahan = $idBahan;
                }
                $r->nama_bahan = $name;
                $r->qty = $qty;
                $r->hitungan = $unit;
                $r->harga = $price;
                $r->save();
            }

            // Jika ada error stok, rollback
            if (! empty($stokErrors)) {
                \Illuminate\Support\Facades\DB::rollBack();

                $errorMessage = count($stokErrors) === 1
                    ? $stokErrors[0]
                    : "Gagal membuat resep:\n".implode("\n", $stokErrors);

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => $stokErrors,
                    ], 422);
                }

                return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
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
            'status' => ucfirst($r->status ?? 'draft'),
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
        \Illuminate\Support\Facades\Log::info("=== UPDATE RESEP #{$id} - STOK TIDAK DIKURANGI DI RESEP (dikurangi saat transaksi) ===");

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

            // STOK BAHAN BAKU DIKURANGI SAAT TRANSAKSI, BUKAN SAAT UPDATE RESEP
            // STEP 1: Tidak perlu kembalikan stok lama
            $oldStatus = $resep->status;
            $restoreStock = false;

            if ($restoreStock) {
                $oldRincian = \App\Models\RincianResep::where('id_resep', $resep->id)->get();

                foreach ($oldRincian as $old) {
                    $name = trim($old->nama_bahan ?? '');
                    if (empty($name)) {
                        continue;
                    }

                    $bahan = \App\Models\BahanBaku::with('konversi')->where('nama', $name)->first();
                    if (! $bahan) {
                        continue;
                    }

                    $oldQty = (float) ($old->qty ?? 0);
                    $oldUnit = strtolower(trim($old->hitungan ?? ''));
                    $bahanSatuan = $this->getBahanSatuan($bahan);

                    // Konversi qty resep lama ke satuan bahan
                    $convertedQty = $this->convertUnit($oldQty, $oldUnit, $bahanSatuan);

                    if ($convertedQty !== null && $convertedQty > 0) {
                        // Kembalikan stok
                        $bahan->stok += $convertedQty;
                        $bahan->tglupdate = now();
                        $bahan->save();

                        \Illuminate\Support\Facades\Log::info("Update Resep '{$resep->nama}': Stok '{$bahan->nama}' dikembalikan {$oldQty} {$oldUnit} = {$convertedQty} {$bahanSatuan}. Stok sekarang: {$bahan->stok} {$bahanSatuan}");
                    }
                }
            }

            // normalize category to match DB enum values
            $category = $this->normalizeCategory($data['category'] ?? $resep->kategori);

            // Get new status
            $newStatus = strtolower(trim($data['status'] ?? $resep->status));

            $resep->update([
                'nama' => $data['name'],
                'porsi' => $data['yield'] ?? $resep->porsi,
                'kategori' => $category,
                'waktu_pembuatan' => $data['duration'] ?? $resep->waktu_pembuatan,
                'langkah' => $data['instructions'] ?? $resep->langkah,
                'catatan' => $data['notes'] ?? $resep->catatan,
                'status' => $newStatus,
                'harga_jual' => isset($data['sellingPrice']) ? $data['sellingPrice'] : $resep->harga_jual,
            ]);

            // Delete old rincian after stock restoration
            \App\Models\RincianResep::where('id_resep', $resep->id)->delete();

            $hasIdBahan = \Illuminate\Support\Facades\Schema::hasColumn('rincian_resep', 'id_bahan');

            // STEP 2: Tidak kurangi stok (stok dikurangi saat transaksi)
            $reduceStock = false;
            $stokErrors = [];

            foreach ($ingredients as $ing) {
                $name = trim($ing['name'] ?? '');
                $qty = (float) ($ing['quantity'] ?? 0);
                $unit = strtolower(trim($ing['unit'] ?? ''));
                $price = (int) ($ing['price'] ?? 0);

                $idBahan = null;
                $bahan = null;

                if ($name) {
                    $bahan = \App\Models\BahanBaku::with('konversi')->where('nama', $name)->first();
                    if (! $bahan) {
                        // Cari konversi berdasarkan satuan_kecil yang sesuai dengan unit resep
                        $unitNormalized = strtolower(trim($unit ?? 'gram'));

                        $konversiDefault = \App\Models\Konversi::where('satuan_kecil', $unitNormalized)->first();

                        // Jika tidak ada, cari konversi default gram atau yang pertama
                        if (! $konversiDefault) {
                            $konversiDefault = \App\Models\Konversi::where('satuan_kecil', 'gram')
                                ->orWhere('satuan_kecil', 'kg')
                                ->orWhere('satuan_kecil', 'pcs')
                                ->first();
                        }

                        $bahan = \App\Models\BahanBaku::create([
                            'nama' => $name,
                            'stok' => 0,
                            'kategori' => null,
                            'min_stok' => 0,
                            'harga_satuan' => $price,
                            'tglupdate' => now(),
                            'id_konversi' => $konversiDefault ? $konversiDefault->id : 1,
                        ]);
                        $bahan->load('konversi');
                    }
                    $idBahan = $bahan->id;

                    // Kurangi stok jika status bukan draft
                    if ($reduceStock && $qty > 0) {
                        $bahanSatuan = $this->getBahanSatuan($bahan);
                        $resepSatuan = $unit;

                        // Konversi qty resep ke satuan bahan
                        $convertedQty = $this->convertUnit($qty, $resepSatuan, $bahanSatuan);

                        if ($convertedQty === null) {
                            $stokErrors[] = "Tidak dapat mengkonversi {$qty} {$resepSatuan} ke {$bahanSatuan} untuk bahan '{$name}'";
                        } elseif ($bahan->stok < $convertedQty) {
                            $stokErrors[] = "Stok '{$name}' tidak cukup. Dibutuhkan: {$convertedQty} {$bahanSatuan}, Tersedia: {$bahan->stok} {$bahanSatuan}";
                        } else {
                            // Kurangi stok
                            $bahan->stok -= $convertedQty;
                            $bahan->tglupdate = now();
                            $bahan->save();

                            \Illuminate\Support\Facades\Log::info("Update Resep '{$resep->nama}': Stok '{$bahan->nama}' berkurang {$qty} {$resepSatuan} = {$convertedQty} {$bahanSatuan}. Sisa: {$bahan->stok} {$bahanSatuan}");
                        }
                    }
                }

                $r = new \App\Models\RincianResep;
                $r->id_resep = $resep->id;
                if ($hasIdBahan && $idBahan) {
                    $r->id_bahan = $idBahan;
                }
                $r->nama_bahan = $name;
                $r->qty = $qty;
                $r->hitungan = $unit;
                $r->harga = $price;
                $r->save();
            }

            // Rollback jika ada error stok
            if (! empty($stokErrors)) {
                \Illuminate\Support\Facades\DB::rollBack();

                $errorMsg = "Gagal mengupdate resep:\n".implode("\n", $stokErrors);

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMsg,
                        'errors' => $stokErrors,
                    ], 422);
                }

                return redirect()->back()->withErrors(['error' => $errorMsg])->withInput();
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
