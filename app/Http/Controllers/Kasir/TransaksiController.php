<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DetailTransaksi;
use App\Models\Jurnal;
use App\Models\Produk;
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
        $totalProduk = $produks->count();
        $customers = Customer::orderBy('nama', 'asc')->get();

        return view('kasir.transaksi.index', compact('produks', 'totalProduk', 'customers'));
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
                'items.*.id' => 'required|exists:produk,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'ppn' => 'nullable|numeric|min:0',
                'diskon' => 'nullable|numeric|min:0',
                'bayar' => 'nullable|numeric|min:0',
                'kembalian' => 'nullable|numeric|min:0',
            ]);

            DB::beginTransaction();

            // Hitung total
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $ppn = $validated['ppn'] ?? ($subtotal * 0.1);
            $diskon = $validated['diskon'] ?? 0;
            $total = $subtotal + $ppn - $diskon;

            // Buat transaksi
            $transaksi = Transaksi::create([
                'id_user' => Auth::id(),
                'id_customer' => $validated['id_customer'] ?? null,
                'tgl' => now(),
                'metode' => $validated['metode'],
                'ppn' => $ppn,
                'diskon' => $diskon,
                'bayar' => $validated['bayar'] ?? $total,
                'kembalian' => $validated['kembalian'] ?? 0,
            ]);

            // Simpan detail transaksi dan kurangi stok
            foreach ($validated['items'] as $item) {
                // Cek stok produk
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

                // ============ PERBAIKAN UTAMA ============
                // BUAT LOG PENGURANGAN STOK UNTUK DETAIL PRODUK
                // Pastikan log benar-benar dibuat
                UpdateStokProduk::create([
                    'id_produk' => $item['id'],
                    'stok_awal' => $stokAwal,
                    'stok_baru' => -$item['quantity'], // Negatif karena pengurangan
                    'total_stok' => $stokAkhir,
                    'kadaluarsa' => $produk->kadaluarsa,
                    'tanggal_update' => now(),
                    'keterangan' => 'Pengurangan stok dari transaksi #'.$transaksi->id,
                ]);
                // =========================================

                // Log untuk debugging
                Log::info("Log stok TRANSAKSI dibuat: Produk ID {$item['id']}, Stok Awal: {$stokAwal}, Pengurangan: {$item['quantity']}, Stok Akhir: {$stokAkhir}");
            }

            // ============ TAMBAHKAN ENTRY JURNAL OTOMATIS ============
            // Buat entry jurnal untuk pemasukan dari penjualan
            Jurnal::create([
                'tgl' => now(),
                'jenis' => 'pemasukan',
                'keterangan' => 'Penjualan - Invoice INV-'.str_pad($transaksi->id, 5, '0', STR_PAD_LEFT).' ('.$validated['metode'].')',
                'nominal' => $total,
                'kategori' => 'Penjualan',
                'role' => 'kasir',
            ]);

            Log::info("Entry jurnal OTOMATIS dibuat untuk transaksi ID: {$transaksi->id}, Total: {$total}");
            // =========================================================

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => [
                    'invoice' => 'INV-'.str_pad($transaksi->id, 5, '0', STR_PAD_LEFT),
                    'transaksi_id' => $transaksi->id,
                    'total' => $total,
                    'kembalian' => $validated['kembalian'] ?? 0,
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
        //
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

    /**
     * Get next transaction ID
     */
    public function getNextId()
    {
        // Get the last transaction ID and increment by 1
        $lastTransaction = Transaksi::latest('id')->first();
        $nextId = $lastTransaction ? $lastTransaction->id + 1 : 1;

        return response()->json([
            'success' => true,
            'next_id' => $nextId,
        ]);
    }
}
