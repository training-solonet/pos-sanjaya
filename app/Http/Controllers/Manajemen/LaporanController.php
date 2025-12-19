<?php

namespace App\Http\Controllers\manajemen;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    //
    public function index()
    {
        // Get filter dates from request or use defaults
        $startDate = request('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = request('end_date', Carbon::now()->toDateString());

        // Validate and parse dates
        try {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfDay();
        }

        // Total penjualan dengan filter tanggal
        $totalSales = Transaksi::whereBetween('tgl', [$startDate, $endDate])->sum('bayar') ?? 0;

        // Jumlah transaksi dengan filter
        $totalTransactions = Transaksi::whereBetween('tgl', [$startDate, $endDate])->count();

        // Rata-rata per transaksi
        $avgPerTransaction = $totalTransactions ? round($totalSales / $totalTransactions) : 0;

        // Total produk terjual (dengan filter tanggal)
        $totalProductsSold = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->whereBetween('transaksi.tgl', [$startDate, $endDate])
            ->sum('detail_transaksi.jumlah') ?? 0;

        // Top products (dengan filter tanggal)
        $topProducts = DB::table('detail_transaksi')
            ->join('produk', 'detail_transaksi.id_produk', '=', 'produk.id')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->whereBetween('transaksi.tgl', [$startDate, $endDate])
            ->select('produk.id', 'produk.nama', DB::raw('SUM(detail_transaksi.jumlah) as total_qty'), DB::raw('SUM(detail_transaksi.jumlah * detail_transaksi.harga) as revenue'))
            ->groupBy('produk.id', 'produk.nama')
            ->orderByDesc('total_qty')
            ->limit(6)
            ->get();

        // Determine top seller (by qty) from the dataset
        $topSeller = $topProducts->first();
        $topSellerName = $topSeller->nama ?? null;
        $topSellerQty = $topSeller->total_qty ?? 0;

        // Determine highest revenue product (dengan filter tanggal)
        $highestRevenueRow = DB::table('detail_transaksi')
            ->join('produk', 'detail_transaksi.id_produk', '=', 'produk.id')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->whereBetween('transaksi.tgl', [$startDate, $endDate])
            ->select('produk.nama', DB::raw('SUM(detail_transaksi.jumlah * detail_transaksi.harga) as revenue'))
            ->groupBy('produk.nama')
            ->orderByDesc('revenue')
            ->first();

        $highestRevenueName = $highestRevenueRow->nama ?? null;
        $highestRevenueValue = $highestRevenueRow->revenue ?? 0;

        // Sales chart: last 30 days
        $labels = [];
        $data = [];
        $today = Carbon::today();
        for ($i = 29; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i);
            $labels[] = $day->format('d');
            $sum = Transaksi::whereDate('tgl', $day->toDateString())->sum('bayar');
            $data[] = (int) $sum;
        }

        $salesChart = [
            'labels' => $labels,
            'data' => $data,
        ];

        // Products chart data
        $productLabels = $topProducts->pluck('nama')->toArray();
        $productQty = $topProducts->pluck('total_qty')->map(fn ($v) => (int) $v)->toArray();
        $productRevenue = $topProducts->pluck('revenue')->map(fn ($v) => (int) $v)->toArray();
        $colors = ['#EF4444', '#F97316', '#F59E0B', '#84CC16', '#10B981', '#06B6D4'];
        $productColors = array_slice($colors, 0, count($productLabels));

        $productsChart = [
            'labels' => $productLabels,
            'dataQty' => $productQty,
            'dataRevenue' => $productRevenue,
            'colors' => $productColors,
        ];

        // PERHITUNGAN GROWTH (PERTUMBUHAN) BULANAN
        // Menghitung persentase pertumbuhan penjualan bulan ini dibanding bulan lalu
        
        // Monthly report (this month vs previous month)
        $currentMonthStart = Carbon::now()->startOfMonth();
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $currentTotal = Transaksi::whereBetween('tgl', [$currentMonthStart->toDateString(), Carbon::now()->toDateString()])->sum('bayar');
        $previousTotal = Transaksi::whereBetween('tgl', [$previousMonthStart->toDateString(), $currentMonthStart->copy()->subDay()->toDateString()])->sum('bayar');

        // RUMUS GROWTH: ((Penjualan Bulan Ini - Penjualan Bulan Lalu) / Penjualan Bulan Lalu) × 100%
        // Calculate growth: if previous month has data, calculate percentage, otherwise show as new growth
        if ($previousTotal > 0) {
            $growth = round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1);
        } elseif ($currentTotal > 0) {
            $growth = 100; // 100% growth from zero
        } else {
            $growth = null; // No data at all
        }

        // PERHITUNGAN PROFIT MARGIN (MARGIN KEUNTUNGAN) BULANAN
       
        // Profit Margin mengukur persentase keuntungan dari total penjualan
        // RUMUS: ((Total Penjualan - Total HPP/Biaya) / Total Penjualan) × 100%
        $currentMonthTransactions = Transaksi::whereBetween('tgl', [$currentMonthStart->toDateString(), Carbon::now()->toDateString()])->pluck('id');
        $totalRevenue = $currentTotal; // Total Penjualan Bulanan
        $totalCost = DB::table('detail_transaksi') // Total HPP/Biaya Bulanan
            ->whereIn('id_transaksi', $currentMonthTransactions)
            ->sum(DB::raw('harga * jumlah'));

        // PERHITUNGAN MARGIN: (Penjualan - HPP) / Penjualan × 100%
        $profitMargin = $totalRevenue > 0 ? round((($totalRevenue - $totalCost) / $totalRevenue) * 100, 1) : null;
       

        $monthlyReport = [
            'monthLabel' => Carbon::now()->format('F Y'),
            'total' => (int) $currentTotal,
            'growthPercent' => $growth,
            'previousTotal' => (int) $previousTotal,
            'profitMargin' => $profitMargin,
        ];

       
        // PERHITUNGAN PROFIT MARGIN (MARGIN KEUNTUNGAN) HARIAN
        // Menghitung profit margin untuk hari ini berdasarkan HPP dari resep
        $todayStart = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();
        
        // Total penjualan hari ini
        $todayTotal = Transaksi::whereBetween('tgl', [$todayStart, $todayEnd])->sum('bayar') ?? 0;
        $todayTransactions = Transaksi::whereBetween('tgl', [$todayStart, $todayEnd])->pluck('id');
        
        // PERHITUNGAN HPP (HARGA POKOK PENJUALAN) HARIAN
        // HPP dihitung dari harga bahan baku yang digunakan dalam resep produk yang terjual
        $todayCost = DB::table('detail_transaksi')
            ->join('produk', 'detail_transaksi.id_produk', '=', 'produk.id')
            ->leftJoin('resep', 'produk.id', '=', 'resep.id_produk')
            ->leftJoin('rincian_resep', 'resep.id', '=', 'rincian_resep.id_resep')
            ->whereIn('detail_transaksi.id_transaksi', $todayTransactions)
            ->select(
                DB::raw('SUM(COALESCE(rincian_resep.harga, detail_transaksi.harga * 0.6) * detail_transaksi.jumlah) as total_cost')
            )
            ->value('total_cost') ?? 0;
        
        // Jika tidak ada data HPP dari resep, gunakan estimasi 60% dari harga jual
        if ($todayCost == 0 && $todayTotal > 0) {
            $todayCost = $todayTotal * 0.6; // Estimasi HPP 60% dari harga jual
        }
        
        // RUMUS PROFIT MARGIN HARIAN: ((Penjualan Hari Ini - HPP Hari Ini) / Penjualan Hari Ini) × 100%
        $todayProfitMargin = $todayTotal > 0 ? round((($todayTotal - $todayCost) / $todayTotal) * 100, 1) : null;
        
        $dailyReport = [
            'date' => Carbon::today()->format('Y-m-d'),
            'totalSales' => (int) $todayTotal,
            'totalCost' => (int) $todayCost,
            'profit' => (int) ($todayTotal - $todayCost),
            'profitMargin' => $todayProfitMargin,
        ];

        return view('manajemen.laporan.index', compact(
            'totalSales',
            'totalTransactions',
            'avgPerTransaction',
            'totalProductsSold',
            'topProducts',
            'salesChart',
            'productsChart',
            'monthlyReport',
            'dailyReport',
            'topSellerName',
            'topSellerQty',
            'highestRevenueName',
            'highestRevenueValue',
            'startDate',
            'endDate'
        ));
    }
}
