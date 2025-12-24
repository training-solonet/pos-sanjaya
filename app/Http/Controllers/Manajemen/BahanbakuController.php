<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use App\Models\Konversi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BahanbakuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Menggunakan pagination dengan 12 data per halaman
        $bahan_baku = BahanBaku::with('konversi')
            ->orderBy('tglupdate', 'desc')
            ->paginate(12); // Ubah get() menjadi paginate(12)

        $konversi = Konversi::all();

        return view('manajemen.bahanbaku.index', compact('bahan_baku', 'konversi'));
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
            'id_konversi' => 'required|exists:konversi,id',
            'satuan_input' => 'required|in:kecil,besar',
        ]);

        try {
            // Ambil data konversi
            $konversi = Konversi::find($validated['id_konversi']);

            if (! $konversi) {
                throw new \Exception('Data konversi tidak ditemukan');
            }

            // Konversi stok berdasarkan pilihan satuan input
            if ($validated['satuan_input'] == 'besar') {
                // Jika input dalam satuan besar, konversi ke satuan kecil
                $stokDalamSatuanKecil = $validated['stok'] * $konversi->jumlah;
                $minStokDalamSatuanKecil = $validated['min_stok'] * $konversi->jumlah;

                Log::info('Create Bahan - Input besar: '.
                    "Stok: {$validated['stok']} {$konversi->satuan_besar} = ".
                    "{$stokDalamSatuanKecil} {$konversi->satuan_kecil}");
            } else {
                // Jika input dalam satuan kecil, simpan langsung
                $stokDalamSatuanKecil = $validated['stok'];
                $minStokDalamSatuanKecil = $validated['min_stok'];

                Log::info('Create Bahan - Input kecil: '.
                    "Stok: {$validated['stok']} {$konversi->satuan_kecil}");
            }

            // Create bahan baku
            BahanBaku::create([
                'nama' => $validated['nama'],
                'stok' => $stokDalamSatuanKecil, // Simpan dalam satuan kecil
                'min_stok' => $minStokDalamSatuanKecil, // Simpan dalam satuan kecil
                'kategori' => $validated['kategori'],
                'harga_satuan' => $validated['harga_satuan'], // Harga per satuan besar
                'id_konversi' => $validated['id_konversi'],
                'tglupdate' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bahan baku berhasil ditambahkan',
                'data' => [
                    'stok_kecil' => $stokDalamSatuanKecil,
                    'stok_besar' => $validated['satuan_input'] == 'besar' ? $validated['stok'] : floor($stokDalamSatuanKecil / $konversi->jumlah),
                    'satuan_kecil' => $konversi->satuan_kecil,
                    'satuan_besar' => $konversi->satuan_besar,
                    'konversi' => $konversi->jumlah,
                ],
            ], 201);

        } catch (\Exception $e) {
            Log::error('Store Bahan Baku Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan bahan baku: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $bahan_baku = BahanBaku::with('konversi')->findOrFail($id);

            return response()->json($bahan_baku);
        } catch (\Exception $e) {
            Log::error('Show Bahan Baku Error: '.$e->getMessage());

            return response()->json([
                'error' => 'Bahan baku tidak ditemukan',
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
        // Cek jika ini operasi tambah stok
        if ($request->has('tambah_stok') && $request->tambah_stok > 0) {
            return $this->tambahStok($request, $id);
        }

        // Validasi untuk update biasa
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:bahan_baku,nama,'.$id,
            'stok' => 'required|integer|min:0',
            'min_stok' => 'required|integer|min:0',
            'kategori' => 'required|in:Bahan Utama,Bahan Pembantu',
            'harga_satuan' => 'required|integer|min:0',
            'id_konversi' => 'required|exists:konversi,id',
        ]);

        try {
            $bahan_baku = BahanBaku::findOrFail($id);

            // Update dengan nilai stok asli (dalam satuan kecil)
            $bahan_baku->update([
                'nama' => $validated['nama'],
                'stok' => $validated['stok'], // Simpan dalam satuan kecil yang diinput user
                'min_stok' => $validated['min_stok'], // Simpan dalam satuan kecil yang diinput user
                'kategori' => $validated['kategori'],
                'harga_satuan' => $validated['harga_satuan'],
                'id_konversi' => $validated['id_konversi'],
                'tglupdate' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bahan baku berhasil diupdate',
            ]);

        } catch (\Exception $e) {
            Log::error('Update Bahan Baku Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate bahan baku: '.$e->getMessage(),
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
                    'message' => 'Tidak dapat menghapus bahan baku karena masih digunakan dalam produk',
                ], 400);
            }

            $bahan_baku->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bahan baku berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            Log::error('Delete Bahan Baku Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus bahan baku: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * API untuk mendapatkan data bahan baku
     */
    public function apiBahanBaku()
    {
        try {
            // Menambahkan orderBy untuk API juga
            $bahan_baku = BahanBaku::with('konversi')
                ->select('id', 'nama', 'id_konversi')
                ->orderBy('tglupdate', 'desc')
                ->get();

            return response()->json($bahan_baku);
        } catch (\Exception $e) {
            Log::error('API Bahan Baku Error: '.$e->getMessage());

            return response()->json([
                'error' => 'Gagal mengambil data bahan baku',
            ], 500);
        }
    }

    /**
     * Tambah stok bahan baku - sekarang bagian dari update
     */
    private function tambahStok(Request $request, $id)
    {
        $validated = $request->validate([
            'tambah_stok' => 'required|integer|min:1',
            'satuan_input' => 'required|in:kecil,besar',
        ]);

        try {
            $bahan_baku = BahanBaku::findOrFail($id);
            $konversi = Konversi::find($bahan_baku->id_konversi);

            if (! $konversi) {
                throw new \Exception('Data konversi tidak ditemukan');
            }

            // Konversi stok tambahan berdasarkan pilihan satuan input
            $tambahStokDalamSatuanKecil = 0;
            if ($validated['satuan_input'] == 'besar') {
                // Jika input dalam satuan besar, konversi ke satuan kecil
                $tambahStokDalamSatuanKecil = $validated['tambah_stok'] * $konversi->jumlah;
                $logInfo = "Menambahkan {$validated['tambah_stok']} {$konversi->satuan_besar} = ".
                          "{$tambahStokDalamSatuanKecil} {$konversi->satuan_kecil}";
            } else {
                // Jika input dalam satuan kecil, langsung tambahkan
                $tambahStokDalamSatuanKecil = $validated['tambah_stok'];
                $logInfo = "Menambahkan {$validated['tambah_stok']} {$konversi->satuan_kecil}";
            }

            Log::info('Tambah Stok: '.$logInfo.' untuk bahan '.$bahan_baku->nama);

            // Hitung stok baru
            $stokBaru = $bahan_baku->stok + $tambahStokDalamSatuanKecil;

            $bahan_baku->update([
                'stok' => $stokBaru,
                'tglupdate' => now(),
            ]);

            // Hitung perhitungan untuk response
            $stokDalamSatuanBesar = floor($stokBaru / $konversi->jumlah);
            $sisaStok = $stokBaru % $konversi->jumlah;

            $displayStok = "{$stokBaru} {$konversi->satuan_kecil}";
            if ($sisaStok > 0) {
                $displayKonversi = "{$stokDalamSatuanBesar} {$konversi->satuan_besar} + {$sisaStok} {$konversi->satuan_kecil}";
            } else {
                $displayKonversi = "{$stokDalamSatuanBesar} {$konversi->satuan_besar}";
            }

            return response()->json([
                'success' => true,
                'message' => "Stok berhasil ditambahkan. Stok sekarang: {$displayStok} ({$displayKonversi})",
                'data' => [
                    'stok_baru' => $stokBaru,
                    'stok_display' => $displayStok,
                    'konversi_display' => $displayKonversi,
                    'tambahan_kecil' => $tambahStokDalamSatuanKecil,
                    'tambahan_besar' => $validated['satuan_input'] == 'besar' ? $validated['tambah_stok'] : round($tambahStokDalamSatuanKecil / $konversi->jumlah, 2),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Tambah Stok Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah stok: '.$e->getMessage(),
            ], 500);
        }
    }
}
