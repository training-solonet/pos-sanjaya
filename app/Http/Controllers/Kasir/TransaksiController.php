<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
// PASTIKAN INI ADA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// PASTIKAN INI ADA

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produks = Produk::where('stok', '>', 0)->get();
        $totalProduk = $produks->count();

        return view('kasir.transaksi.index', compact('produks', 'totalProduk'));
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
            }

            // Catat ke jurnal secara otomatis
            $invoiceNumber = 'INV-'.str_pad($transaksi->id, 5, '0', STR_PAD_LEFT);

            // Buat deskripsi produk yang dibeli
            $keterangan = 'Penjualan ';
            $itemDescriptions = [];
            foreach ($validated['items'] as $item) {
                $produk = Produk::find($item['id']);
                $itemDescriptions[] = $produk->nama.' x'.$item['quantity'];
            }
            $keterangan .= implode(', ', $itemDescriptions);

            Jurnal::create([
                'tgl' => now(),
                'jenis' => 'pemasukan',
                'kategori' => 'Penjualan',
                'keterangan' => $keterangan,
                'nominal' => $total,
                'role' => 'kasir',
            ]);

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
