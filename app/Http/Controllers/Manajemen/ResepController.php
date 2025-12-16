<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $bahans = DB::table('bahan_baku')
            ->join('konversi', 'bahan_baku.id_konversi', '=', 'konversi.id')
            ->select('bahan_baku.id', 'bahan_baku.nama', 'bahan_baku.stok', 'bahan_baku.harga_satuan', 'konversi.satuan_kecil')
            ->get()
            ->toArray();
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
            'gram' => ['kg' => 0.001, 'g' => 1, 'slice' => 0.1, 'pcs' => 1],
            'g' => ['kg' => 0.001, 'gram' => 1, 'slice' => 0.1, 'pcs' => 1],

            // Volume
            'l' => ['ml' => 1000, 'liter' => 1],
            'liter' => ['ml' => 1000, 'l' => 1],
            'ml' => ['l' => 0.001, 'liter' => 0.001],

            // Sendok
            'sdm' => ['ml' => 15, 'gram' => 15, 'g' => 15, 'sdt' => 3],
            'sdt' => ['ml' => 5, 'gram' => 5, 'g' => 5, 'sdm' => 0.333],

            // Slice
            'slice' => ['gram' => 10, 'g' => 10],

            // Pcs (pieces)
            'pcs' => ['gram' => 1, 'g' => 1],
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

            // VALIDASI STOK BAHAN BAKU SEBELUM MENYIMPAN RESEP
            $status = strtolower($data['status'] ?? 'draft');
            $stokErrors = [];

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

            // Jika status aktif dan produk punya stok, validasi bahan baku harus cukup
            if ($status === 'aktif' && $produk->stok > 0) {
                foreach ($ingredients as $ing) {
                    $name = trim($ing['name'] ?? '');
                    $qty = isset($ing['quantity']) ? (float) $ing['quantity'] : 0;
                    $unit = strtolower(trim($ing['unit'] ?? 'gram'));

                    if (empty($name) || $qty <= 0) {
                        continue;
                    }

                    // Cari bahan di database
                    $bahan = DB::table('bahan_baku')
                        ->join('konversi', 'bahan_baku.id_konversi', '=', 'konversi.id')
                        ->where('bahan_baku.nama', $name)
                        ->select('bahan_baku.*', 'konversi.satuan_kecil')
                        ->first();

                    if (! $bahan) {
                        $stokErrors[] = "Bahan '{$name}' tidak ditemukan di database.";

                        continue;
                    }

                    // Hitung kebutuhan total untuk stok produk yang ada
                    $totalQty = $qty * $produk->stok;
                    $satuanStok = strtolower($bahan->satuan_kecil ?? 'gram');

                    // Konversi satuan resep ke satuan stok
                    $convertedQty = $this->convertUnit($totalQty, $unit, $satuanStok);

                    if ($convertedQty === null) {
                        $stokErrors[] = "Konversi gagal untuk bahan '{$name}': {$unit} → {$satuanStok}. Gunakan satuan yang sesuai.";

                        continue;
                    }

                    // Cek apakah stok cukup
                    if ($bahan->stok < $convertedQty) {
                        $stokErrors[] = "Stok {$name} tidak cukup! Dibutuhkan {$convertedQty} {$satuanStok}, tersedia {$bahan->stok} {$satuanStok}.";
                    }
                }

                // Jika ada error stok, rollback dan kirim error
                if (! empty($stokErrors)) {
                    DB::rollBack();
                    $errorMessage = "Resep tidak dapat disimpan karena stok bahan baku tidak mencukupi:\n".implode("\n", $stokErrors);

                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => $stokErrors,
                        ], 422);
                    }

                    return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
                }
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
            // STOK BAHAN BAKU HANYA DIKURANGI SAAT TAMBAH STOK PRODUK, BUKAN SAAT BUAT/UPDATE RESEP

            \Illuminate\Support\Facades\Log::info("CREATE RESEP '{$resep->nama}': Status = '{$resep->status}', TIDAK ada pengurangan stok (stok dikurangi saat tambah stok produk)");

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

                    // TIDAK kurangi stok di sini - stok HANYA dikurangi saat tambah stok produk
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

            // Commit - tidak ada validasi stok karena stok HANYA dikurangi saat tambah stok produk
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

            // SINKRONISASI: Kurangi stok bahan baku sesuai stok produk yang sudah ada
            if ($resep->status === 'aktif' && $produk->stok > 0) {
                \Illuminate\Support\Facades\Log::info("=== SINKRONISASI RESEP BARU: Kurangi bahan baku untuk {$produk->stok} produk yang sudah ada ===");
                try {
                    $this->reduceBahanBakuFromResep($produk, $produk->stok, $resep->id);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error sinkronisasi stok: '.$e->getMessage());
                }
            }

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

            // Simpan status lama untuk cek perubahan
            $oldStatus = $resep->status;

            // Get new status
            $newStatus = strtolower(trim($data['status'] ?? $resep->status));

            // VALIDASI STOK BAHAN BAKU SEBELUM UPDATE RESEP
            $produk = \App\Models\Produk::find($resep->id_produk);

            // Jika status berubah jadi aktif dan produk punya stok, validasi bahan baku harus cukup
            if ($newStatus === 'aktif' && $oldStatus !== 'aktif' && $produk && $produk->stok > 0) {
                $stokErrors = [];

                foreach ($ingredients as $ing) {
                    $name = trim($ing['name'] ?? '');
                    $qty = isset($ing['quantity']) ? (float) $ing['quantity'] : 0;
                    $unit = strtolower(trim($ing['unit'] ?? 'gram'));

                    if (empty($name) || $qty <= 0) {
                        continue;
                    }

                    // Cari bahan di database
                    $bahan = DB::table('bahan_baku')
                        ->join('konversi', 'bahan_baku.id_konversi', '=', 'konversi.id')
                        ->where('bahan_baku.nama', $name)
                        ->select('bahan_baku.*', 'konversi.satuan_kecil')
                        ->first();

                    if (! $bahan) {
                        $stokErrors[] = "Bahan '{$name}' tidak ditemukan di database.";

                        continue;
                    }

                    // Hitung kebutuhan total untuk stok produk yang ada
                    $totalQty = $qty * $produk->stok;
                    $satuanStok = strtolower($bahan->satuan_kecil ?? 'gram');

                    // Konversi satuan resep ke satuan stok
                    $convertedQty = $this->convertUnit($totalQty, $unit, $satuanStok);

                    if ($convertedQty === null) {
                        $stokErrors[] = "Konversi gagal untuk bahan '{$name}': {$unit} → {$satuanStok}. Gunakan satuan yang sesuai.";

                        continue;
                    }

                    // Cek apakah stok cukup
                    if ($bahan->stok < $convertedQty) {
                        $stokErrors[] = "Stok {$name} tidak cukup! Dibutuhkan {$convertedQty} {$satuanStok}, tersedia {$bahan->stok} {$satuanStok}.";
                    }
                }

                // Jika ada error stok, rollback dan kirim error
                if (! empty($stokErrors)) {
                    DB::rollBack();
                    $errorMessage = "Resep tidak dapat diaktifkan karena stok bahan baku tidak mencukupi:\n".implode("\n", $stokErrors);

                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage,
                            'errors' => $stokErrors,
                        ], 422);
                    }

                    return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
                }
            }

            // normalize category to match DB enum values
            $category = $this->normalizeCategory($data['category'] ?? $resep->kategori);

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

            // SINKRONISASI: Jika status berubah jadi aktif, kurangi stok bahan baku
            if ($newStatus === 'aktif' && $oldStatus !== 'aktif' && $produk && $produk->stok > 0) {
                \Illuminate\Support\Facades\Log::info("=== SINKRONISASI UPDATE RESEP: Status aktif, kurangi bahan untuk {$produk->stok} produk ===");
                try {
                    $this->reduceBahanBakuFromResep($produk, $produk->stok, $resep->id);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error sinkronisasi update: '.$e->getMessage());
                }
            }

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

    /**
     * Kurangi stok bahan baku berdasarkan resep produk
     */
    private function reduceBahanBakuFromResep($produk, $jumlahProduk, $resepId)
    {
        try {
            \Illuminate\Support\Facades\Log::info(">>> Sinkronisasi stok: Produk '{$produk->nama}' × {$jumlahProduk}");

            // Ambil rincian resep
            $rincianReseps = \Illuminate\Support\Facades\DB::table('rincian_resep')
                ->where('id_resep', $resepId)
                ->get();

            foreach ($rincianReseps as $rincian) {
                $namaBahan = $rincian->nama_bahan;
                $qtyPer1 = (float) $rincian->qty;
                $unit = strtolower(trim($rincian->hitungan ?? 'gram'));

                $totalQty = $qtyPer1 * $jumlahProduk;

                \Illuminate\Support\Facades\Log::info("  → {$namaBahan}: {$qtyPer1} {$unit} × {$jumlahProduk} = {$totalQty} {$unit}");

                // Cari bahan
                $bahan = \Illuminate\Support\Facades\DB::table('bahan_baku')
                    ->join('konversi', 'bahan_baku.id_konversi', '=', 'konversi.id')
                    ->where('bahan_baku.nama', $namaBahan)
                    ->select('bahan_baku.*', 'konversi.satuan_kecil')
                    ->first();

                if (! $bahan) {
                    \Illuminate\Support\Facades\Log::warning("Bahan '{$namaBahan}' tidak ditemukan");

                    continue;
                }

                $satuanStok = strtolower($bahan->satuan_kecil ?? 'gram');
                $convertedQty = $this->convertUnit($totalQty, $unit, $satuanStok);

                if ($convertedQty === null) {
                    \Illuminate\Support\Facades\Log::error("Konversi gagal: {$totalQty} {$unit} → {$satuanStok}");

                    continue;
                }

                if ($bahan->stok < $convertedQty) {
                    \Illuminate\Support\Facades\Log::error("Stok {$namaBahan} tidak cukup: {$bahan->stok} < {$convertedQty}");

                    continue;
                }

                $stokBaru = $bahan->stok - $convertedQty;
                \Illuminate\Support\Facades\DB::table('bahan_baku')
                    ->where('id', $bahan->id)
                    ->update([
                        'stok' => $stokBaru,
                        'tglupdate' => now(),
                        'updated_at' => now(),
                    ]);

                \Illuminate\Support\Facades\Log::info("  ✓ {$namaBahan} berkurang {$convertedQty} {$satuanStok}. Sisa: {$stokBaru}");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error reduceBahanBaku: '.$e->getMessage());
            throw $e;
        }
    }
}
