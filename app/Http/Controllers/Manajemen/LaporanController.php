<?php

namespace App\Http\Controllers\manajemen;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use Carbon\Carbon;

class LaporanController extends Controller
{
    //
    public function index()
    {
        // Total penjualan (sum bayar)
        $totalSales = Transaksi::sum('bayar') ?? 0;

        // Jumlah transaksi
        $totalTransactions = Transaksi::count();

        // Rata-rata per transaksi
        $avgPerTransaction = $totalTransactions ? round($totalSales / $totalTransactions) : 0;

        // Total produk terjual (semua qty)
        $totalProductsSold = DB::table('detail_transaksi')->sum('qty') ?? 0;

        // Top products (qty and revenue)
        $topProducts = DB::table('detail_transaksi')
            ->join('produk', 'detail_transaksi.id_produk', '=', 'produk.id')
            ->select('produk.id', 'produk.nama', DB::raw('SUM(detail_transaksi.qty) as total_qty'), DB::raw('SUM(detail_transaksi.qty * detail_transaksi.harga) as revenue'))
            ->groupBy('produk.id', 'produk.nama')
            ->orderByDesc('total_qty')
            ->limit(6)
            ->get();

        // Determine top seller (by qty) from the dataset
        $topSeller = $topProducts->first();
        $topSellerName = $topSeller->nama ?? null;
        $topSellerQty = $topSeller->total_qty ?? 0;

        // Determine highest revenue product (may be outside the top-qty list)
        $highestRevenueRow = DB::table('detail_transaksi')
            ->join('produk', 'detail_transaksi.id_produk', '=', 'produk.id')
            ->select('produk.nama', DB::raw('SUM(detail_transaksi.qty * detail_transaksi.harga) as revenue'))
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
        $productQty = $topProducts->pluck('total_qty')->map(fn($v) => (int)$v)->toArray();
        $productRevenue = $topProducts->pluck('revenue')->map(fn($v) => (int)$v)->toArray();
        $colors = ['#EF4444','#F97316','#F59E0B','#84CC16','#10B981','#06B6D4'];
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
        $growth = $previousTotal > 0 ? round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1) : null;

        $monthlyReport = [
            'monthLabel' => Carbon::now()->format('F Y'),
            'total' => (int)$currentTotal,
            'growthPercent' => $growth,
            'previousTotal' => (int)$previousTotal,
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
            'highestRevenueValue'
        ));
    }
}
