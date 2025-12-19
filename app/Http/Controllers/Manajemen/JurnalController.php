<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        // Jika request meminta data JSON (untuk AJAX)
        if ($request->ajax() || $request->has('data')) {
            return $this->getData($request);
        }

        // Jika request meminta summary
        if ($request->has('summary')) {
            return $this->getSummary($request);
        }

        // Ambil data jurnal untuk hari ini (default view) - GABUNGAN data manual dan transaksi
        $today = now()->format('Y-m-d');

        // Ambil data jurnal manual (role manajemen)
        $jurnalsManual = Jurnal::whereDate('tgl', $today)
            ->where('role', 'manajemen')
            ->orderBy('tgl', 'desc')
            ->get();

        // Ambil data transaksi hari ini dan konversi ke format jurnal
        $transaksiHariIni = Transaksi::whereDate('tgl', $today)->get();

        // Gabungkan data
        $jurnals = $jurnalsManual->concat($this->convertTransactionsToJurnals($transaksiHariIni))
            ->sortByDesc('tgl');

        // Hitung total penjualan hari ini UNTUK TAMPILAN DI JURNAL
        $todaySales = Transaksi::whereDate('tgl', $today)
            ->sum(DB::raw('bayar - kembalian'));

        // Hitung total transaksi hari ini
        $todayTransactions = Transaksi::whereDate('tgl', $today)->count();

        return view('manajemen.jurnal.index', compact('jurnals', 'todaySales', 'todayTransactions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl' => 'required|date',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'keterangan' => 'required|string|max:500',
            'nominal' => 'required|integer|min:1',
            'kategori' => 'required|in:Operasional,Utilitas,Bahan Baku,Penjualan,Transportasi,lainnya',
        ]);

        try {
            Jurnal::create([
                'tgl' => $validated['tgl'],
                'jenis' => $validated['jenis'],
                'keterangan' => $validated['keterangan'],
                'nominal' => $validated['nominal'],
                'kategori' => $validated['kategori'],
                'role' => 'manajemen',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil ditambahkan!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah transaksi: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Cek apakah ID adalah transaksi (format: transaksi_{id})
            if (str_starts_with($id, 'transaksi_')) {
                $transaksiId = str_replace('transaksi_', '', $id);
                $transaksi = Transaksi::findOrFail($transaksiId);

                $jurnalData = [
                    'id' => 'transaksi_'.$transaksi->id,
                    'tgl' => $transaksi->tgl,
                    'jenis' => 'pemasukan',
                    'keterangan' => 'Penjualan - Transaksi #'.str_pad($transaksi->id, 5, '0', STR_PAD_LEFT),
                    'nominal' => $transaksi->bayar - $transaksi->kembalian,
                    'kategori' => 'Penjualan',
                    'role' => 'admin',
                    'is_transaction' => true,
                ];

                return response()->json($jurnalData);
            }

            // Jika bukan transaksi, ambil dari jurnal manual
            $jurnal = Jurnal::where('id', $id)
                ->where('role', 'manajemen')
                ->firstOrFail();

            return response()->json($jurnal);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Transaksi tidak ditemukan',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        // Cek apakah ID adalah transaksi (tidak boleh edit transaksi dari kasir)
        if (str_starts_with($id, 'transaksi_')) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi dari kasir tidak dapat diedit dari halaman ini',
            ], 403);
        }

        $validated = $request->validate([
            'tgl' => 'required|date',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'keterangan' => 'required|string|max:500',
            'nominal' => 'required|integer|min:1',
            'kategori' => 'required|in:Operasional,Utilitas,Bahan Baku,Penjualan,Transportasi,lainnya',
        ]);

        try {
            $jurnal = Jurnal::where('id', $id)
                ->where('role', 'manajemen')
                ->firstOrFail();

            $jurnal->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diupdate!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate transaksi: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        // Cek apakah ID adalah transaksi (tidak boleh hapus transaksi dari kasir)
        if (str_starts_with($id, 'transaksi_')) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi dari kasir tidak dapat dihapus dari halaman ini',
            ], 403);
        }

        try {
            $jurnal = Jurnal::where('id', $id)
                ->where('role', 'manajemen')
                ->firstOrFail();

            $jurnal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: '.$e->getMessage(),
            ], 500);
        }
    }

    // Method untuk mendapatkan data jurnal berdasarkan filter - sekarang termasuk transaksi
    private function getData(Request $request)
    {
        $query = Jurnal::where('role', 'manajemen');

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('tgl', $request->date);
        }

        // Filter by jenis
        if ($request->has('jenis') && $request->jenis != 'semua') {
            $query->where('jenis', $request->jenis);
        }

        // Filter by kategori
        if ($request->has('kategori') && $request->kategori != 'semua') {
            $query->where('kategori', $request->kategori);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('keterangan', 'like', '%'.$request->search.'%');
        }

        $jurnalsManual = $query->orderBy('tgl', 'desc')->get();

        // Ambil transaksi berdasarkan filter yang sama
        $transaksiQuery = Transaksi::query();

        if ($request->has('date') && $request->date) {
            $transaksiQuery->whereDate('tgl', $request->date);
        }

        // Untuk jenis, transaksi selalu pemasukan
        if ($request->has('jenis') && $request->jenis != 'semua') {
            if ($request->jenis == 'pemasukan') {
                // Hanya ambil jika filter pemasukan
                $transaksiQuery->whereRaw('1=1');
            } else {
                // Jika filter pengeluaran, jangan ambil transaksi
                $transaksiQuery->whereRaw('1=0');
            }
        }

        // Untuk kategori, transaksi selalu Penjualan
        if ($request->has('kategori') && $request->kategori != 'semua') {
            if ($request->kategori == 'Penjualan') {
                $transaksiQuery->whereRaw('1=1');
            } else {
                $transaksiQuery->whereRaw('1=0');
            }
        }

        if ($request->has('search') && $request->search) {
            $transaksiQuery->where('id', 'like', '%'.$request->search.'%');
        }

        $transaksis = $transaksiQuery->orderBy('tgl', 'desc')->get();

        // Konversi transaksi ke format jurnal
        $jurnalsTransaksi = $this->convertTransactionsToJurnals($transaksis);

        // Gabungkan dan urutkan
        $allJurnals = $jurnalsManual->concat($jurnalsTransaksi)
            ->sortByDesc('tgl')
            ->values();

        return response()->json($allJurnals);
    }

    // Method untuk mendapatkan summary - sekarang termasuk transaksi
    private function getSummary(Request $request)
    {
        $date = $request->has('date') && $request->date ? $request->date : now()->format('Y-m-d');

        // Summary dari jurnal manual
        $manualQuery = Jurnal::where('role', 'manajemen')
            ->whereDate('tgl', $date);

        $manualData = $manualQuery->select(
            DB::raw('COUNT(*) as total_transaksi'),
            DB::raw('SUM(CASE WHEN jenis = "pemasukan" THEN nominal ELSE 0 END) as total_pemasukan'),
            DB::raw('SUM(CASE WHEN jenis = "pengeluaran" THEN nominal ELSE 0 END) as total_pengeluaran'),
            DB::raw('COUNT(CASE WHEN jenis = "pemasukan" THEN 1 END) as jumlah_pemasukan'),
            DB::raw('COUNT(CASE WHEN jenis = "pengeluaran" THEN 1 END) as jumlah_pengeluaran')
        )->first();

        // Summary dari transaksi
        $transaksiQuery = Transaksi::whereDate('tgl', $date);
        $transaksiData = $transaksiQuery->select(
            DB::raw('COUNT(*) as jumlah_transaksi'),
            DB::raw('SUM(bayar - kembalian) as total_penjualan')
        )->first();

        // Gabungkan data
        $totalPemasukan = ($manualData->total_pemasukan ?? 0) + ($transaksiData->total_penjualan ?? 0);
        $totalPengeluaran = $manualData->total_pengeluaran ?? 0;
        $jumlahPemasukan = ($manualData->jumlah_pemasukan ?? 0) + ($transaksiData->jumlah_transaksi ?? 0);
        $jumlahPengeluaran = $manualData->jumlah_pengeluaran ?? 0;
        $totalTransaksi = ($manualData->total_transaksi ?? 0) + ($transaksiData->jumlah_transaksi ?? 0);

        return response()->json([
            'total_revenue' => $totalPemasukan,
            'total_expense' => $totalPengeluaran,
            'net_balance' => $totalPemasukan - $totalPengeluaran,
            'revenue_count' => $jumlahPemasukan,
            'expense_count' => $jumlahPengeluaran,
            'total_transactions' => $totalTransaksi,
        ]);
    }

    // Helper method: Konversi transaksi ke format jurnal
    private function convertTransactionsToJurnals($transaksis)
    {
        return $transaksis->map(function ($transaksi) {
            return [
                'id' => 'transaksi_'.$transaksi->id,
                'tgl' => $transaksi->tgl,
                'jenis' => 'pemasukan',
                'keterangan' => 'Penjualan - Transaksi #'.str_pad($transaksi->id, 5, '0', STR_PAD_LEFT),
                'nominal' => $transaksi->bayar - $transaksi->kembalian,
                'kategori' => 'Penjualan',
                'role' => 'admin',
                'is_transaction' => true,
                'created_at' => $transaksi->created_at,
                'updated_at' => $transaksi->updated_at,
            ];
        });
    }

    // Method create dan edit (kosong karena menggunakan modal)
    public function create() {}

    public function edit($id) {}
}
