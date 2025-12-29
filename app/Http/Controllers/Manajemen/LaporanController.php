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
        if ($previousTotal > 0) {
            $growth = round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1);
        } elseif ($currentTotal > 0) {
            $growth = 100; // 100% growth from zero
        } else {
            $growth = null; // No data at all
        }

        // PERHITUNGAN PROFIT MARGIN (MARGIN KEUNTUNGAN) BULANAN

        // Profit Margin mengukur persentase keuntungan dari total penjualan
        // RUMUS: ((Total Penjualan - Total HPP/FoodCost) / Total Penjualan) × 100%
        $currentMonthTransactions = Transaksi::whereBetween('tgl', [$currentMonthStart->toDateString(), Carbon::now()->toDateString()])->pluck('id');
        $totalRevenue = $currentTotal; // Total Penjualan Bulanan

        // Hitung Total HPP (FoodCost) dari rincian_resep untuk produk yang terjual bulan ini
        // FoodCost per produk = SUM(qty * harga) dari rincian_resep
        $totalCost = DB::table('detail_transaksi')
            ->join('produk', 'detail_transaksi.id_produk', '=', 'produk.id')
            ->leftJoin('resep', 'produk.id', '=', 'resep.id_produk')
            ->whereIn('detail_transaksi.id_transaksi', $currentMonthTransactions)
            ->select(
                DB::raw('SUM(
                    detail_transaksi.jumlah * 
                    COALESCE(
                        (SELECT SUM(rr.qty * rr.harga) 
                         FROM rincian_resep rr 
                         WHERE rr.id_resep = resep.id),
                        0
                    )
                ) as total_cost')
            )
            ->value('total_cost') ?? 0;

        // PERHITUNGAN MARGIN: (Penjualan - FoodCost) / Penjualan × 100%
        // Hanya hitung profit margin jika ada penjualan yang sebenarnya
        $profitMargin = $totalRevenue > 0 && $totalCost >= 0 ? round((($totalRevenue - $totalCost) / $totalRevenue) * 100, 1) : null;

        $monthlyReport = [
            'monthLabel' => Carbon::now()->format('F Y'),
            'total' => (int) $currentTotal,
            'growthPercent' => $growth,
            'previousTotal' => (int) $previousTotal,
            'profitMargin' => $profitMargin,
        ];

        // PERHITUNGAN PROFIT MARGIN (MARGIN KEUNTUNGAN) HARIAN
        // Menghitung profit margin untuk hari ini berdasarkan FoodCost dari resep
        $todayStart = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();

        // Total penjualan hari ini
        $todayTotal = Transaksi::whereBetween('tgl', [$todayStart, $todayEnd])->sum('bayar') ?? 0;
        $todayTransactions = Transaksi::whereBetween('tgl', [$todayStart, $todayEnd])->pluck('id');

        // PERHITUNGAN FOODCOST (HARGA POKOK PENJUALAN) HARIAN
        // FoodCost dihitung dari SUM(qty * harga) di rincian_resep untuk produk yang terjual
        $todayCost = DB::table('detail_transaksi')
            ->join('produk', 'detail_transaksi.id_produk', '=', 'produk.id')
            ->leftJoin('resep', 'produk.id', '=', 'resep.id_produk')
            ->whereIn('detail_transaksi.id_transaksi', $todayTransactions)
            ->select(
                DB::raw('SUM(
                    detail_transaksi.jumlah * 
                    COALESCE(
                        (SELECT SUM(rr.qty * rr.harga) 
                         FROM rincian_resep rr 
                         WHERE rr.id_resep = resep.id),
                        0
                    )
                ) as total_cost')
            )
            ->value('total_cost') ?? 0;

        // RUMUS PROFIT MARGIN HARIAN: ((Penjualan Hari Ini - FoodCost Hari Ini) / Penjualan Hari Ini) × 100%
        // Hanya hitung profit margin jika ada penjualan yang sebenarnya
        if ($todayTotal > 0 && $todayCost >= 0) {
            $todayProfitMargin = round((($todayTotal - $todayCost) / $todayTotal) * 100, 1);
        } else {
            $todayProfitMargin = null; // Tidak ada penjualan atau data tidak valid
        }

        $dailyReport = [
            'date' => Carbon::today()->format('Y-m-d'),
            'totalSales' => (int) $todayTotal,
            'totalCost' => (int) $todayCost,
            'profit' => (int) ($todayTotal - $todayCost),
            'profitMargin' => $todayProfitMargin,
        ];

        // PERHITUNGAN LAPORAN MINGGUAN (WEEKLY REPORT)
        // Menghitung penjualan minggu ini vs minggu lalu
        $thisWeekStart = Carbon::now()->startOfWeek(); // Senin minggu ini
        $thisWeekEnd = Carbon::now()->endOfWeek(); // Minggu minggu ini
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek(); // Senin minggu lalu
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek(); // Minggu minggu lalu

        // Total penjualan minggu ini
        $thisWeekTotal = Transaksi::whereBetween('tgl', [$thisWeekStart, $thisWeekEnd])->sum('bayar') ?? 0;

        // Total penjualan minggu lalu
        $lastWeekTotal = Transaksi::whereBetween('tgl', [$lastWeekStart, $lastWeekEnd])->sum('bayar') ?? 0;

        // RUMUS GROWTH MINGGUAN: ((Penjualan Minggu Ini - Penjualan Minggu Lalu) / Penjualan Minggu Lalu) × 100%
        if ($lastWeekTotal > 0) {
            $weeklyGrowth = round((($thisWeekTotal - $lastWeekTotal) / $lastWeekTotal) * 100, 1);
        } elseif ($thisWeekTotal > 0) {
            $weeklyGrowth = 100; // 100% growth from zero
        } else {
            $weeklyGrowth = null; // No data at all
        }

        // Hitung FoodCost minggu ini untuk profit margin
        $thisWeekTransactions = Transaksi::whereBetween('tgl', [$thisWeekStart, $thisWeekEnd])->pluck('id');
        $thisWeekCost = DB::table('detail_transaksi')
            ->join('produk', 'detail_transaksi.id_produk', '=', 'produk.id')
            ->leftJoin('resep', 'produk.id', '=', 'resep.id_produk')
            ->whereIn('detail_transaksi.id_transaksi', $thisWeekTransactions)
            ->select(
                DB::raw('SUM(
                    detail_transaksi.jumlah * 
                    COALESCE(
                        (SELECT SUM(rr.qty * rr.harga) 
                         FROM rincian_resep rr 
                         WHERE rr.id_resep = resep.id),
                        0
                    )
                ) as total_cost')
            )
            ->value('total_cost') ?? 0;

        // RUMUS PROFIT MARGIN MINGGUAN: ((Penjualan Minggu Ini - FoodCost Minggu Ini) / Penjualan Minggu Ini) × 100%
        // Hanya hitung profit margin jika ada penjualan yang sebenarnya
        if ($thisWeekTotal > 0 && $thisWeekCost >= 0) {
            $weeklyProfitMargin = round((($thisWeekTotal - $thisWeekCost) / $thisWeekTotal) * 100, 1);
        } else {
            $weeklyProfitMargin = null; // Tidak ada penjualan atau data tidak valid
        }

        $weeklyReport = [
            'weekLabel' => $thisWeekStart->format('d M').' - '.$thisWeekEnd->format('d M Y'),
            'total' => (int) $thisWeekTotal,
            'growthPercent' => $weeklyGrowth,
            'lastWeekTotal' => (int) $lastWeekTotal,
            'profitMargin' => $weeklyProfitMargin,
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
            'weeklyReport',
            'topSellerName',
            'topSellerQty',
            'highestRevenueName',
            'highestRevenueValue',
            'startDate',
            'endDate'
        ));
    }
}
