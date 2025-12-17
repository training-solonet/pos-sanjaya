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

        // Monthly report (this month vs previous month)
        $currentMonthStart = Carbon::now()->startOfMonth();
        $previousMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $currentTotal = Transaksi::whereBetween('tgl', [$currentMonthStart->toDateString(), Carbon::now()->toDateString()])->sum('bayar');
        $previousTotal = Transaksi::whereBetween('tgl', [$previousMonthStart->toDateString(), $currentMonthStart->copy()->subDay()->toDateString()])->sum('bayar');
        
        // Calculate growth: if previous month has data, calculate percentage, otherwise show as new growth
        if ($previousTotal > 0) {
            $growth = round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1);
        } elseif ($currentTotal > 0) {
            $growth = 100; // 100% growth from zero
        } else {
            $growth = null; // No data at all
        }

        // Calculate profit margin (assuming 30% average profit margin for simplicity)
        // In real scenario, you would calculate: (Revenue - Cost) / Revenue * 100
        // For now, we use a simple calculation based on detail_transaksi total
        $currentMonthTransactions = Transaksi::whereBetween('tgl', [$currentMonthStart->toDateString(), Carbon::now()->toDateString()])->pluck('id');
        $totalRevenue = $currentTotal;
        $totalCost = DB::table('detail_transaksi')
            ->whereIn('id_transaksi', $currentMonthTransactions)
            ->sum(DB::raw('harga * jumlah'));
        
        $profitMargin = $totalRevenue > 0 ? round((($totalRevenue - $totalCost) / $totalRevenue) * 100, 1) : null;

        $monthlyReport = [
            'monthLabel' => Carbon::now()->format('F Y'),
            'total' => (int) $currentTotal,
            'growthPercent' => $growth,
            'previousTotal' => (int) $previousTotal,
            'profitMargin' => $profitMargin,
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
            'topSellerName',
            'topSellerQty',
            'highestRevenueName',
            'highestRevenueValue',
            'startDate',
            'endDate'
        ));
    }
}
