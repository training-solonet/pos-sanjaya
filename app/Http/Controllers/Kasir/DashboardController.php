<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get today's date
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Penjualan Hari Ini (Total from bayar field)
        $penjualanHariIni = Transaksi::whereDate('tgl', $today)->sum('bayar');

        // Penjualan Kemarin
        $penjualanKemarin = Transaksi::whereDate('tgl', $yesterday)->sum('bayar');

        // Hitung persentase perubahan
        $persenPenjualan = $penjualanKemarin > 0
            ? round((($penjualanHariIni - $penjualanKemarin) / $penjualanKemarin) * 100, 1)
            : 0;

        // 2. Total Transaksi Hari Ini
        $totalTransaksiHariIni = Transaksi::whereDate('tgl', $today)->count();

        // Total Transaksi Kemarin
        $totalTransaksiKemarin = Transaksi::whereDate('tgl', $yesterday)->count();

        // Hitung persentase perubahan
        $persenTransaksi = $totalTransaksiKemarin > 0
            ? round((($totalTransaksiHariIni - $totalTransaksiKemarin) / $totalTransaksiKemarin) * 100, 1)
            : 0;

        // 3. Produk Terjual Hari Ini (Total jumlah from detail_transaksi)
        $produkTerjualHariIni = DetailTransaksi::whereHas('transaksi', function ($query) use ($today) {
            $query->whereDate('tgl', $today);
        })->sum('jumlah');

        // Produk Terjual Kemarin
        $produkTerjualKemarin = DetailTransaksi::whereHas('transaksi', function ($query) use ($yesterday) {
            $query->whereDate('tgl', $yesterday);
        })->sum('jumlah');

        // Hitung persentase perubahan
        $persenProdukTerjual = $produkTerjualKemarin > 0
            ? round((($produkTerjualHariIni - $produkTerjualKemarin) / $produkTerjualKemarin) * 100, 1)
            : 0;

        // 4. Stok Rendah (produk dengan stok <= min_stok)
        $stokRendah = Produk::whereRaw('stok <= min_stok')->count();

        // 5. Transaksi Terakhir (3 transaksi terbaru hari ini)
        $transaksiTerakhir = Transaksi::with('detailTransaksi.produk')
            ->whereDate('tgl', $today)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // 6. Produk Terlaris (3 produk dengan penjualan terbanyak hari ini)
        $produkTerlaris = DetailTransaksi::select('id_produk', DB::raw('SUM(jumlah) as total_terjual'))
            ->whereHas('transaksi', function ($query) use ($today) {
                $query->whereDate('tgl', $today);
            })
            ->with('produk')
            ->groupBy('id_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(3)
            ->get();

        // 6a. Data untuk grafik produk terlaris (top 5)
        $produkTerlarisChart = DetailTransaksi::select('id_produk', DB::raw('SUM(jumlah) as total_terjual'))
            ->whereHas('transaksi', function ($query) use ($today) {
                $query->whereDate('tgl', $today);
            })
            ->with('produk')
            ->groupBy('id_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(5)
            ->get();

        // 7. Data untuk grafik penjualan 7 hari terakhir
        $penjualan7Hari = [];
        $transaksi7Hari = [];
        $labels7Hari = [];
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $penjualan7Hari[] = Transaksi::whereDate('tgl', $date)->sum('bayar');
            $transaksi7Hari[] = Transaksi::whereDate('tgl', $date)->count();
            $labels7Hari[] = $dayNames[$date->dayOfWeek];
        }

        // 7a. Data untuk grafik penjualan 30 hari terakhir
        $penjualan30Hari = [];
        $transaksi30Hari = [];
        $labels30Hari = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $penjualan30Hari[] = Transaksi::whereDate('tgl', $date)->sum('bayar');
            $transaksi30Hari[] = Transaksi::whereDate('tgl', $date)->count();
            $labels30Hari[] = $date->format('d/m');
        }

        // 8. Data penjualan harian 7 hari terakhir (untuk diagram batang)
        $penjualanPerHari = [];
        $labelHari = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labelHari[] = $dayNames[$date->dayOfWeek];

            $total = Transaksi::whereDate('tgl', $date)
                ->sum('bayar');

            $penjualanPerHari[] = $total;
        }

        return view('kasir.dashboard', compact(
            'penjualanHariIni',
            'persenPenjualan',
            'totalTransaksiHariIni',
            'persenTransaksi',
            'produkTerjualHariIni',
            'persenProdukTerjual',
            'stokRendah',
            'transaksiTerakhir',
            'produkTerlaris',
            'produkTerlarisChart',
            'penjualan7Hari',
            'transaksi7Hari',
            'labels7Hari',
            'penjualan30Hari',
            'transaksi30Hari',
            'labels30Hari',
            'penjualanPerHari',
            'labelHari'
        ));
    }

    /**
     * Get chart data for specific period via AJAX
     */
    public function getChartData(Request $request)
    {
        $period = $request->input('period', '7days');
        $days = $period === '7days' ? 7 : 30;

        $penjualan = [];
        $transaksi = [];
        $labels = [];
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $penjualan[] = Transaksi::whereDate('tgl', $date)->sum('bayar');
            $transaksi[] = Transaksi::whereDate('tgl', $date)->count();

            if ($period === '7days') {
                $labels[] = $dayNames[$date->dayOfWeek];
            } else {
                $labels[] = $date->format('d/m');
            }
        }

        return response()->json([
            'labels' => $labels,
            'sales' => $penjualan,
            'transactions' => $transaksi,
        ]);
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
        //
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
}
