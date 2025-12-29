<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\UpdateStokProduk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

            // Ambil history update stok dengan relasi produk dan pagination 12 data per halaman
            $history = UpdateStokProduk::with('produk')
                ->orderBy('tanggal_update', 'desc')
                ->paginate(12);

            // Hitung summary dengan timezone Jakarta
            $now = Carbon::now('Asia/Jakarta');
            $totalEntries = UpdateStokProduk::count();

            // Count today entries
            $todayEntries = UpdateStokProduk::whereDate('tanggal_update', $now->toDateString())->count();

            // Count week entries
            $weekEntries = UpdateStokProduk::whereBetween('tanggal_update',
                [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]
            )->count();

            // Count month entries
            $monthEntries = UpdateStokProduk::whereBetween('tanggal_update',
                [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]
            )->count();

            return view('manajemen.produk.UpdateStokProduk', compact(
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
        Log::info('==========================================');
        Log::info('=== STORE UPDATE STOK DIPANGGIL ===');
        Log::info('Store Update Stok Request:', $request->all());
        Log::info('==========================================');

        $request->validate([
            'id_produk' => 'required|exists:produk,id',
            'stok_baru' => 'required|integer|min:1',
            'kadaluarsa' => 'required|date|after_or_equal:today',
            'keterangan' => 'nullable|string|max:500',
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
                    'keterangan' => $request->keterangan,
                ]);
                // Tambahan code unntuk resep ke bahan baku
                Log::info('==========================================');
                Log::info('=== MULAI KURANGI STOK BAHAN BAKU ===');
                Log::info('==========================================');

                // KURANGI STOK BAHAN BAKU BERDASARKAN RESEP
                try {
                    $this->reduceBahanBakuFromResep($produk, $request->stok_baru);
                    Log::info('==========================================');
                    Log::info('=== SELESAI KURANGI STOK BAHAN BAKU ===');
                    Log::info('==========================================');
                } catch (\Exception $e) {
                    Log::error('==========================================');
                    Log::error('=== ERROR KURANGI STOK BAHAN BAKU ===');
                    Log::error('Error: '.$e->getMessage());
                    Log::error('==========================================');
                    // Jangan throw agar transaksi produk tetap berhasil
                    // throw $e;
                }
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
                    'keterangan' => $updateStok->keterangan,
                ],
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
            'keterangan' => 'nullable|string|max:500',
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
                    'keterangan' => $request->keterangan,
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
                    'stok' => $produk->stok - $updateStok->stok_baru,
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
            'ids.*' => 'exists:update_stok_produk,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $ids = $request->ids;

                foreach ($ids as $id) {
                    $updateStok = UpdateStokProduk::with('produk')->findOrFail($id);
                    $produk = $updateStok->produk;

                    // Kembalikan stok produk
                    $produk->update([
                        'stok' => $produk->stok - $updateStok->stok_baru,
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

    /**
     * Tambahan Code untuk Triger resep mengurangi bahan baku
     * Kurangi stok bahan baku berdasarkan resep produk
     * Dipanggil saat produk ditambah stoknya
     */
    private function reduceBahanBakuFromResep($produk, $jumlahProduk)
    {
        try {
            Log::info(">>> TAMBAH STOK PRODUK '{$produk->nama}' sebanyak {$jumlahProduk}");

            // Cari resep aktif untuk produk ini
            $resep = DB::table('resep')
                ->where('id_produk', $produk->id)
                ->where('status', 'aktif')
                ->first();

            if (! $resep) {
                Log::warning("Produk '{$produk->nama}' tidak memiliki resep aktif. Stok bahan baku tidak dikurangi.");

                return;
            }

            Log::info("Resep ditemukan: #{$resep->id} - {$resep->nama}");

            // Ambil rincian resep (bahan-bahan untuk 1 produk)
            $rincianReseps = DB::table('rincian_resep')
                ->where('id_resep', $resep->id)
                ->get();

            $errors = [];

            foreach ($rincianReseps as $rincian) {
                $namaBahan = $rincian->nama_bahan;
                $qtyPer1Produk = (float) $rincian->qty;
                $unitResep = strtolower(trim($rincian->hitungan ?? 'gram'));

                // Total bahan yang dibutuhkan = qty per 1 produk × jumlah produk ditambahkan
                $totalQtyNeeded = $qtyPer1Produk * $jumlahProduk;

                Log::info("   → Bahan '{$namaBahan}': {$qtyPer1Produk} {$unitResep}/produk × {$jumlahProduk} produk = {$totalQtyNeeded} {$unitResep}");

                // Cari bahan baku
                $bahan = DB::table('bahan_baku')
                    ->join('konversi', 'bahan_baku.id_konversi', '=', 'konversi.id')
                    ->where('bahan_baku.nama', $namaBahan)
                    ->select('bahan_baku.*', 'konversi.satuan_kecil')
                    ->first();

                if (! $bahan) {
                    $errors[] = "Bahan '{$namaBahan}' tidak ditemukan di database";
                    Log::warning("Bahan '{$namaBahan}' tidak ditemukan");

                    continue;
                }

                // GUNAKAN satuan_kecil dari database langsung (TIDAK dikonversi lagi!)
                // Karena stok di database sudah disimpan dalam satuan_kecil (kg, gram, pcs, dll)
                $satuanStok = strtolower($bahan->satuan_kecil ?? 'gram');

                Log::info("   → Satuan stok bahan '{$namaBahan}': {$satuanStok}, Stok tersedia: {$bahan->stok} {$satuanStok}");

                // Konversi qty resep ke satuan stok database
                $convertedQty = $this->convertUnit($totalQtyNeeded, $unitResep, $satuanStok);

                if ($convertedQty === null) {
                    $errors[] = "Tidak dapat mengkonversi {$totalQtyNeeded} {$unitResep} ke {$satuanStok} untuk '{$namaBahan}'";
                    Log::error("Konversi gagal: {$totalQtyNeeded} {$unitResep} → {$satuanStok}");

                    continue;
                }

                // Cek stok mencukupi
                if ($bahan->stok < $convertedQty) {
                    $errors[] = "Stok bahan '{$namaBahan}' tidak mencukupi. Tersedia: {$bahan->stok} {$satuanStok}, Dibutuhkan: {$convertedQty} {$satuanStok}";
                    Log::error("Stok '{$namaBahan}' tidak cukup: tersedia {$bahan->stok}, butuh {$convertedQty}");

                    continue;
                }

                // Kurangi stok
                $stokBaru = $bahan->stok - $convertedQty;
                DB::table('bahan_baku')
                    ->where('id', $bahan->id)
                    ->update([
                        'stok' => $stokBaru,
                        'tglupdate' => now(),
                        'updated_at' => now(),
                    ]);

                Log::info("✓ Stok '{$namaBahan}' berkurang {$totalQtyNeeded} {$unitResep} = {$convertedQty} {$satuanStok}. Sisa: {$stokBaru} {$satuanStok}");
            }

            if (! empty($errors)) {
                throw new \Exception("Terjadi kesalahan:\n".implode("\n", $errors));
            }

        } catch (\Exception $e) {
            Log::error('ERROR reduceBahanBakuFromResep: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Map satuan ke satuan terkecil (untuk menghindari desimal di integer)
     */
    private function mapToSmallestUnit($satuan)
    {
        $map = [
            'kg' => 'gram',
            'l' => 'ml',
            'liter' => 'ml',
        ];

        return $map[$satuan] ?? $satuan;
    }

    /**
     * Konversi unit
     */
    private function convertUnit($quantity, $fromUnit, $toUnit)
    {
        $fromUnit = strtolower(trim($fromUnit));
        $toUnit = strtolower(trim($toUnit));

        if ($fromUnit === $toUnit) {
            return $quantity;
        }

        $conversions = [
            'kg' => ['gram' => 1000, 'g' => 1000],
            'gram' => ['kg' => 0.001, 'g' => 1, 'slice' => 0.1, 'pcs' => 1],
            'g' => ['kg' => 0.001, 'gram' => 1, 'slice' => 0.1, 'pcs' => 1],
            'l' => ['ml' => 1000, 'liter' => 1],
            'liter' => ['ml' => 1000, 'l' => 1],
            'ml' => ['l' => 0.001, 'liter' => 0.001],
            'sdm' => ['ml' => 15, 'gram' => 15, 'g' => 15],
            'sdt' => ['ml' => 5, 'gram' => 5, 'g' => 5],
            'slice' => ['gram' => 10, 'g' => 10],
            'pcs' => ['gram' => 1, 'g' => 1],
        ];

        if (isset($conversions[$fromUnit][$toUnit])) {
            return $quantity * $conversions[$fromUnit][$toUnit];
        }

        return null;
    }
}
