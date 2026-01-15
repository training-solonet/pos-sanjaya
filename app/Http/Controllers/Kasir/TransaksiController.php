<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DetailTransaksi;
use App\Models\Jurnal;
use App\Models\Pajak;
use App\Models\Produk;
use App\Models\Promo;
use App\Models\Transaksi; // PASTIKAN INI ADA
use App\Models\UpdateStokProduk; // TAMBAHKAN INI UNTUK AUTO JURNAL
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // PASTIKAN INI ADA

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produks = Produk::where('stok', '>', 0)->get();
        $totalProduk = Produk::where('stok', '>', 0)->count();
        $customers = Customer::orderBy('nama', 'asc')->get();

        // Ambil data bundle (promo dengan bundle products) yang aktif dan memiliki stok
        // Filter berdasarkan tanggal start_date dan end_date
        $today = now()->toDateString();
        $bundles = Promo::where('status', true)
            ->where('jenis', 'bundle')
            ->where('stok', '>', 0)
            ->where(function ($query) use ($today) {
                $query->where(function ($q) use ($today) {
                    // Bundle dengan start_date dan end_date yang valid
                    $q->whereNotNull('start_date')
                        ->whereNotNull('end_date')
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today);
                })
                    ->orWhere(function ($q) {
                        // Bundle tanpa batas tanggal (start_date dan end_date null)
                        $q->whereNull('start_date')
                            ->whereNull('end_date');
                    });
            })
            ->with(['bundleProducts.produk'])
            ->whereHas('bundleProducts')
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil pajak aktif
        $pajak = Pajak::where('status', true)->first();

        // Ambil promo diskon aktif (bukan bundle) dengan filter tanggal
        $promos = Promo::where('status', true)
            ->whereIn('jenis', ['diskon_persen', 'cashback'])
            ->where(function ($query) use ($today) {
                $query->where(function ($q) use ($today) {
                    // Promo dengan start_date dan end_date yang valid
                    $q->whereNotNull('start_date')
                        ->whereNotNull('end_date')
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today);
                })
                    ->orWhere(function ($q) {
                        // Promo tanpa batas tanggal (start_date dan end_date null)
                        $q->whereNull('start_date')
                            ->whereNull('end_date');
                    });
            })
            ->orderBy('start_date', 'asc')
            ->get();

        return view('kasir.transaksi.index', compact('produks', 'totalProduk', 'customers', 'bundles', 'pajak', 'promos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Cek apakah user sudah login
            if (! Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User belum login. Silakan login terlebih dahulu.',
                ], 401);
            }

            // Validasi input
            $validated = $request->validate([
                'metode' => 'required|in:tunai,kartu,transfer,qris',
                'id_customer' => 'nullable|exists:data_customer,id',
                'items' => 'required|array|min:1',
                'items.*.id' => 'required',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.isBundle' => 'nullable|boolean',
                'items.*.bundleProducts' => 'nullable|array',
                'ppn' => 'nullable|numeric|min:0',
                'diskon' => 'nullable|numeric|min:0',
                'bayar' => 'nullable|numeric|min:0',
                'kembalian' => 'nullable|numeric|min:0',
                'poin_digunakan' => 'nullable|integer|min:0',
            ]);

            DB::beginTransaction();

            // Hitung total dan poin
            $subtotal = 0;
            $totalBundleQty = 0; // Hitung total quantity bundle yang dibeli
            $subtotalProdukRegular = 0; // Subtotal hanya untuk produk reguler (bukan bundle)

            foreach ($validated['items'] as $item) {
                $subtotal += $item['price'] * $item['quantity'];

                $isBundle = $item['isBundle'] ?? false;
                if ($isBundle) {
                    $totalBundleQty += $item['quantity'];
                } else {
                    $subtotalProdukRegular += $item['price'] * $item['quantity'];
                }
            }

            $ppn = $validated['ppn'] ?? ($subtotal * 0.1);
            $diskon = $validated['diskon'] ?? 0;
            $poinDigunakan = $validated['poin_digunakan'] ?? 0;

            // Total = Subtotal + PPN - Diskon - Poin Digunakan (1 poin = Rp 1)
            $total = $subtotal + $ppn - $diskon - $poinDigunakan;

            // Hitung poin yang didapat
            // - Produk reguler: 5 poin per Rp 10.000
            // - Bundle: 10 poin per bundle (tanpa terpaku harga)
            $poinDariProdukRegular = floor($subtotalProdukRegular / 10000) * 5;
            $poinDariBundle = $totalBundleQty * 10;
            $totalPoinDidapat = $poinDariProdukRegular + $poinDariBundle;

            // Generate ID Transaksi Unik (11 karakter: T + 10 digit angka)
            // Format: TXXXXXXXXXX (T + timestamp 10 digit)
            $timestamp = now()->timestamp; // Unix timestamp
            $idTransaksi = 'T'.substr($timestamp, -10); // Ambil 10 digit terakhir dari timestamp

            // Buat transaksi
            // Kolom 'bayar' berisi total yang harus dibayar (bukan uang yang diterima)
            // Kolom 'kembalian' berisi selisih antara uang diterima dengan total
            $transaksi = Transaksi::create([
                'id_transaksi' => $idTransaksi,
                'id_user' => Auth::id(),
                'id_customer' => $validated['id_customer'] ?? null,
                'tgl' => now(),
                'metode' => $validated['metode'],
                'ppn' => $ppn,
                'diskon' => $diskon,
                'bayar' => $total, // Total yang harus dibayar
                'kembalian' => $validated['kembalian'] ?? 0,
            ]);

            // Simpan detail transaksi dan kurangi stok
            foreach ($validated['items'] as $item) {
                $isBundle = $item['isBundle'] ?? false;

                if ($isBundle) {
                    // Handle bundle product
                    $bundleProducts = $item['bundleProducts'] ?? [];

                    // Validasi bundleProducts
                    if (empty($bundleProducts)) {
                        Log::error('Bundle products empty for item: ', $item);
                        throw new \Exception('Data bundle tidak valid. Bundle harus memiliki minimal 1 produk.');
                    }

                    // Cek dan kurangi stok bundle di tabel Promo
                    $bundlePromo = Promo::findOrFail($item['id']);
                    if ($bundlePromo->stok < $item['quantity']) {
                        throw new \Exception("Stok bundle {$bundlePromo->nama_promo} tidak mencukupi. Stok tersedia: {$bundlePromo->stok}");
                    }

                    // Kurangi stok bundle
                    $bundlePromo->decrement('stok', $item['quantity']);
                    Log::info("Stok bundle dikurangi: Bundle ID {$bundlePromo->id}, Qty: {$item['quantity']}, Stok tersisa: {$bundlePromo->stok}");

                    // Kurangi stok untuk setiap produk dalam bundle
                    foreach ($bundleProducts as $bundleItem) {
                        // Validasi struktur bundle item
                        if (! isset($bundleItem['id_produk']) || ! isset($bundleItem['quantity'])) {
                            Log::error('Invalid bundle item structure: ', $bundleItem);
                            throw new \Exception('Struktur data bundle tidak valid.');
                        }

                        $produk = Produk::findOrFail($bundleItem['id_produk']);
                        $qtyNeeded = $bundleItem['quantity'] * $item['quantity'];

                        if ($produk->stok < $qtyNeeded) {
                            throw new \Exception("Stok {$produk->nama} tidak mencukupi untuk bundle. Stok tersedia: {$produk->stok}, diperlukan: {$qtyNeeded}");
                        }

                        // Simpan stok awal
                        $stokAwal = $produk->stok;

                        // Kurangi stok
                        $produk->decrement('stok', $qtyNeeded);

                        // Simpan stok akhir
                        $stokAkhir = $produk->stok;

                        // Log pengurangan stok
                        UpdateStokProduk::create([
                            'id_produk' => $produk->id,
                            'stok_awal' => $stokAwal,
                            'stok_baru' => -$qtyNeeded,
                            'total_stok' => $stokAkhir,
                            'kadaluarsa' => $produk->kadaluarsa,
                            'tanggal_update' => now(),
                            'keterangan' => "Pengurangan stok dari bundle transaksi #{$transaksi->id}",
                        ]);

                        Log::info("Log stok BUNDLE dibuat: Produk ID {$produk->id}, Stok Awal: {$stokAwal}, Pengurangan: {$qtyNeeded}, Stok Akhir: {$stokAkhir}");
                    }

                    // Simpan detail transaksi untuk bundle (dengan harga total bundle)
                    // Gunakan produk pertama dalam bundle sebagai representasi
                    $firstBundleProduct = $bundleProducts[0] ?? null;
                    if ($firstBundleProduct) {
                        DetailTransaksi::create([
                            'id_transaksi' => $transaksi->id,
                            'id_produk' => $firstBundleProduct['id_produk'],
                            'jumlah' => $item['quantity'],
                            'harga' => $item['price'],
                        ]);
                    }
                } else {
                    // Handle regular product
                    $produk = Produk::findOrFail($item['id']);

                    if ($produk->stok < $item['quantity']) {
                        throw new \Exception("Stok {$produk->nama} tidak mencukupi. Stok tersedia: {$produk->stok}");
                    }

                    // Simpan detail transaksi
                    DetailTransaksi::create([
                        'id_transaksi' => $transaksi->id,
                        'id_produk' => $item['id'],
                        'jumlah' => $item['quantity'],
                        'harga' => $item['price'],
                    ]);

                    // Simpan stok awal sebelum dikurangi
                    $stokAwal = $produk->stok;

                    // Kurangi stok produk
                    $produk->decrement('stok', $item['quantity']);

                    // Simpan stok akhir setelah dikurangi
                    $stokAkhir = $produk->stok;

                    // BUAT LOG PENGURANGAN STOK UNTUK DETAIL PRODUK
                    UpdateStokProduk::create([
                        'id_produk' => $item['id'],
                        'stok_awal' => $stokAwal,
                        'stok_baru' => -$item['quantity'], // Negatif karena pengurangan
                        'total_stok' => $stokAkhir,
                        'kadaluarsa' => $produk->kadaluarsa,
                        'tanggal_update' => now(),
                        'keterangan' => 'Pengurangan stok dari transaksi #'.$transaksi->id,
                    ]);

                    // Log untuk debugging
                    Log::info("Log stok TRANSAKSI dibuat: Produk ID {$item['id']}, Stok Awal: {$stokAwal}, Pengurangan: {$item['quantity']}, Stok Akhir: {$stokAkhir}");
                }
            }

            // ============ TAMBAHKAN ENTRY JURNAL OTOMATIS ============
            // Buat entry jurnal untuk pemasukan dari penjualan
            Jurnal::create([
                'tgl' => now(),
                'jenis' => 'pemasukan',
                'keterangan' => 'Penjualan - INV '.$transaksi->id_transaksi.' ('.$validated['metode'].')',
                'nominal' => $total,
                'kategori' => 'Penjualan',
                'role' => 'kasir',
            ]);

            Log::info("Entry jurnal OTOMATIS dibuat untuk transaksi ID: {$transaksi->id}, Total: {$total}");
            // =========================================================

            // ============ TAMBAHKAN POIN KE CUSTOMER ============
            // Jika transaksi menggunakan member, kelola poin
            // Poin dihitung: 5 poin per Rp10.000 (produk reguler) + 10 poin per bundle
            if ($validated['id_customer']) {
                $customer = Customer::find($validated['id_customer']);
                if ($customer && $customer->kode_member) {
                    // Kurangi poin yang digunakan terlebih dahulu
                    if ($poinDigunakan > 0) {
                        if ($customer->total_poin >= $poinDigunakan) {
                            $customer->decrement('total_poin', $poinDigunakan);
                            Log::info("Poin digunakan dari customer ID {$customer->id}: {$poinDigunakan} poin");
                        } else {
                            throw new \Exception("Poin customer tidak mencukupi. Poin tersedia: {$customer->total_poin}");
                        }
                    }

                    // Tambahkan poin yang didapat dari transaksi (dihitung di backend)
                    if ($totalPoinDidapat > 0) {
                        $customer->increment('total_poin', $totalPoinDidapat);
                        Log::info("Poin ditambahkan ke customer ID {$customer->id}: {$totalPoinDidapat} poin (Produk: {$poinDariProdukRegular}, Bundle: {$poinDariBundle})");
                    }
                }
            }
            // ==================================================================

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => [
                    'id_transaksi' => $idTransaksi,
                    'invoice' => 'INV-'.str_pad($transaksi->id, 5, '0', STR_PAD_LEFT),
                    'transaksi_id' => $transaksi->id,
                    'total' => $total,
                    'kembalian' => $validated['kembalian'] ?? 0,
                    'poin_didapat' => $totalPoinDidapat ?? 0,
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Redirect to index since we don't have a separate create page
        return redirect()->route('kasir.transaksi.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Redirect to index since we don't edit transactions
        return redirect()->route('kasir.transaksi.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
