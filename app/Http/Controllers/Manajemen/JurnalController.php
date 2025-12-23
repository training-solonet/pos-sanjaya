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
        $transaksiQuery = Transaksi::query();

        // Tentukan tanggal yang akan digunakan
        $date = $request->has('date') && $request->date ? $request->date : now()->format('Y-m-d');
        $period = $request->has('period') ? $request->period : 'daily';

        // Filter berdasarkan periode waktu
        switch ($period) {
            case 'daily':
                // Filter harian
                $query->whereDate('tgl', $date);
                $transaksiQuery->whereDate('tgl', $date);
                break;

            case 'weekly':
                // Filter mingguan (Senin - Minggu)
                $startOfWeek = date('Y-m-d', strtotime('monday this week', strtotime($date)));
                $endOfWeek = date('Y-m-d', strtotime('sunday this week', strtotime($date)));

                $query->whereBetween('tgl', [$startOfWeek, $endOfWeek]);
                $transaksiQuery->whereBetween('tgl', [$startOfWeek, $endOfWeek]);
                break;

            case 'monthly':
                // Filter bulanan
                $startOfMonth = date('Y-m-01', strtotime($date));
                $endOfMonth = date('Y-m-t', strtotime($date));

                $query->whereBetween('tgl', [$startOfMonth, $endOfMonth]);
                $transaksiQuery->whereBetween('tgl', [$startOfMonth, $endOfMonth]);
                break;

            default:
                // Default harian
                $query->whereDate('tgl', $date);
                $transaksiQuery->whereDate('tgl', $date);
        }

        // Filter by jenis
        if ($request->has('jenis') && $request->jenis != 'semua') {
            $query->where('jenis', $request->jenis);

            // Untuk transaksi, hanya ambil jika filter pemasukan
            if ($request->jenis == 'pemasukan') {
                $transaksiQuery->whereRaw('1=1');
            } else {
                $transaksiQuery->whereRaw('1=0');
            }
        }

        // Filter by kategori
        if ($request->has('kategori') && $request->kategori != 'semua') {
            $query->where('kategori', $request->kategori);

            // Untuk transaksi, hanya ambil jika filter Penjualan
            if ($request->kategori == 'Penjualan') {
                $transaksiQuery->whereRaw('1=1');
            } else {
                $transaksiQuery->whereRaw('1=0');
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('keterangan', 'like', '%'.$request->search.'%')
                    ->orWhere('id', 'like', '%'.$request->search.'%');
            });

            $transaksiQuery->where(function ($q) use ($request) {
                $q->where('id', 'like', '%'.$request->search.'%');
            });
        }

        $jurnalsManual = $query->orderBy('tgl', 'desc')->get();
        $transaksis = $transaksiQuery->orderBy('tgl', 'desc')->get();

        // Konversi transaksi ke format jurnal
        $jurnalsTransaksi = $this->convertTransactionsToJurnals($transaksis);

        // Gabungkan dan urutkan
        $allJurnals = $jurnalsManual->concat($jurnalsTransaksi)
            ->sortByDesc('tgl')
            ->values();

        return response()->json($allJurnals);
    }

    // Method untuk mendapatkan summary - sekarang termasuk transaksi (DIPERBAIKI)
    private function getSummary(Request $request)
    {
        $date = $request->has('date') && $request->date ? $request->date : now()->format('Y-m-d');
        $period = $request->has('period') ? $request->period : 'daily';

        // Inisialisasi query terpisah untuk manual dan transaksi
        $manualQuery = Jurnal::where('role', 'manajemen');
        $transaksiQuery = Transaksi::query();

        // Gunakan logika filter yang SAMA PERSIS dengan getData()
        switch ($period) {
            case 'daily':
                // Filter harian
                $manualQuery->whereDate('tgl', $date);
                $transaksiQuery->whereDate('tgl', $date);
                break;

            case 'weekly':
                // Filter mingguan (Senin - Minggu)
                $startOfWeek = date('Y-m-d', strtotime('monday this week', strtotime($date)));
                $endOfWeek = date('Y-m-d', strtotime('sunday this week', strtotime($date)));

                $manualQuery->whereBetween('tgl', [$startOfWeek, $endOfWeek]);
                $transaksiQuery->whereBetween('tgl', [$startOfWeek, $endOfWeek]);
                break;

            case 'monthly':
                // Filter bulanan
                $startOfMonth = date('Y-m-01', strtotime($date));
                $endOfMonth = date('Y-m-t', strtotime($date));

                $manualQuery->whereBetween('tgl', [$startOfMonth, $endOfMonth]);
                $transaksiQuery->whereBetween('tgl', [$startOfMonth, $endOfMonth]);
                break;

            default:
                // Default harian
                $manualQuery->whereDate('tgl', $date);
                $transaksiQuery->whereDate('tgl', $date);
        }

        // Summary dari jurnal manual - gunakan cara yang lebih akurat
        $manualData = $manualQuery->get();

        $manualTotalPemasukan = $manualData->where('jenis', 'pemasukan')->sum('nominal');
        $manualTotalPengeluaran = $manualData->where('jenis', 'pengeluaran')->sum('nominal');
        $manualJumlahPemasukan = $manualData->where('jenis', 'pemasukan')->count();
        $manualJumlahPengeluaran = $manualData->where('jenis', 'pengeluaran')->count();
        $manualTotalTransaksi = $manualData->count();

        // Summary dari transaksi
        $transaksiData = $transaksiQuery->get();

        $transaksiTotalPenjualan = $transaksiData->sum(function ($transaksi) {
            return $transaksi->bayar - $transaksi->kembalian;
        });
        $transaksiJumlah = $transaksiData->count();

        // Gabungkan data dengan BENAR
        $totalPemasukan = $manualTotalPemasukan + $transaksiTotalPenjualan;
        $totalPengeluaran = $manualTotalPengeluaran;
        $jumlahPemasukan = $manualJumlahPemasukan + $transaksiJumlah;
        $jumlahPengeluaran = $manualJumlahPengeluaran;
        $totalTransaksi = $manualTotalTransaksi + $transaksiJumlah;

        // Tentukan rentang tanggal untuk display
        switch ($period) {
            case 'daily':
                $startDate = $date;
                $endDate = $date;
                break;

            case 'weekly':
                $startDate = date('Y-m-d', strtotime('monday this week', strtotime($date)));
                $endDate = date('Y-m-d', strtotime('sunday this week', strtotime($date)));
                break;

            case 'monthly':
                $startDate = date('Y-m-01', strtotime($date));
                $endDate = date('Y-m-t', strtotime($date));
                break;

            default:
                $startDate = $date;
                $endDate = $date;
        }

        return response()->json([
            'total_revenue' => $totalPemasukan,
            'total_expense' => $totalPengeluaran,
            'net_balance' => $totalPemasukan - $totalPengeluaran,
            'revenue_count' => $jumlahPemasukan,
            'expense_count' => $jumlahPengeluaran,
            'total_transactions' => $totalTransaksi,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'period_type' => $period,
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
