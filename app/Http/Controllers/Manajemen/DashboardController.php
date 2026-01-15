<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
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
    public function index(Request $request)
    {
        // Return JSON if requested (for AJAX updates)
        if ($request->ajax() || $request->has('json')) {
            return response()->json($this->getDashboardData());
        }

        // Check if export is requested
        if ($request->has('export')) {
            return $this->exportData($request->get('export'));
        }

        $data = $this->getDashboardData();

        return view('manajemen.dashboard.index', $data);
    }

    /**
     * Get dashboard data for both web and API
     */
    private function getDashboardData()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $sevenDaysAgo = Carbon::today()->subDays(6);
        $thirtyDaysAgo = Carbon::today()->subDays(29);

        // 1. TOTAL PENJUALAN HARI INI
        $todaySales = Transaksi::whereDate('tgl', $today)->sum('bayar') ?? 0;
        $yesterdaySales = Transaksi::whereDate('tgl', $yesterday)->sum('bayar') ?? 0;
        $salesGrowth = $yesterdaySales > 0 ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1) : ($todaySales > 0 ? 100 : 0);

        // 2. TOTAL TRANSAKSI HARI INI
        $todayTransactions = Transaksi::whereDate('tgl', $today)->count();
        $yesterdayTransactions = Transaksi::whereDate('tgl', $yesterday)->count();
        $transactionsGrowth = $yesterdayTransactions > 0 ? round((($todayTransactions - $yesterdayTransactions) / $yesterdayTransactions) * 100, 1) : ($todayTransactions > 0 ? 100 : 0);

        // 3. TOTAL PRODUK TERJUAL HARI INI
        $todayProductsSold = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->whereDate('transaksi.tgl', $today)
            ->sum('detail_transaksi.jumlah') ?? 0;

        $yesterdayProductsSold = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->whereDate('transaksi.tgl', $yesterday)
            ->sum('detail_transaksi.jumlah') ?? 0;

        $productsSoldGrowth = $yesterdayProductsSold > 0 ? round((($todayProductsSold - $yesterdayProductsSold) / $yesterdayProductsSold) * 100, 1) : ($todayProductsSold > 0 ? 100 : 0);

        // 4. STOK RENDAH (PRODUK)
        $lowStockCount = Produk::whereColumn('stok', '<=', 'min_stok')->count();

        // 5. LOW STOCK PRODUCTS
        $lowStockProducts = Produk::select('produk.*', DB::raw('(stok / CASE WHEN min_stok = 0 THEN 1 ELSE min_stok END * 100) as percentage'))
            ->whereColumn('stok', '<=', 'min_stok')
            ->orderBy('percentage', 'ASC')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                $percentage = $product->min_stok > 0 ? round(($product->stok / $product->min_stok) * 100) : 0;

                if ($product->stok == 0) {
                    $statusColor = 'red';
                    $statusText = 'Habis';
                } elseif ($percentage <= 20) {
                    $statusColor = 'red';
                    $statusText = 'Kritis';
                } elseif ($percentage <= 50) {
                    $statusColor = 'orange';
                    $statusText = 'Rendah';
                } else {
                    $statusColor = 'yellow';
                    $statusText = 'Rendah';
                }

                return (object) [
                    'id' => $product->id,
                    'nama' => $product->nama,
                    'stok' => $product->stok,
                    'min_stok' => $product->min_stok,
                    'satuan' => 'pcs',
                    'percentage' => $percentage,
                    'status_color' => $statusColor,
                    'status_text' => $statusText,
                ];
            });

        // 6. TOP PRODUCTS (7 days)
        $topProducts = DB::table('detail_transaksi')
            ->join('produk', 'detail_transaksi.id_produk', '=', 'produk.id')
            ->join('transaksi', 'detail_transaksi.id_transaksi', '=', 'transaksi.id')
            ->select(
                'produk.id',
                'produk.nama',
                'produk.harga',
                DB::raw('SUM(detail_transaksi.jumlah) as total_qty')
            )
            ->whereBetween('transaksi.tgl', [$sevenDaysAgo->toDateString(), $today->toDateString()])
            ->groupBy('produk.id', 'produk.nama', 'produk.harga')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 7. LOW STOCK BAHAN BAKU
        $lowStockBahanBaku = BahanBaku::with('konversi')
            ->whereColumn('stok', '<=', 'min_stok')
            ->orderBy('stok', 'ASC')
            ->limit(5)
            ->get()
            ->map(function ($bahan) {
                $percentage = $bahan->min_stok > 0 ? round(($bahan->stok / $bahan->min_stok) * 100) : 0;

                if ($bahan->stok == 0) {
                    $statusColor = 'red';
                    $statusText = 'Habis';
                } elseif ($percentage <= 20) {
                    $statusColor = 'red';
                    $statusText = 'Kritis';
                } elseif ($percentage <= 50) {
                    $statusColor = 'orange';
                    $statusText = 'Rendah';
                } else {
                    $statusColor = 'yellow';
                    $statusText = 'Rendah';
                }

                // Format stok display
                $konversi = $bahan->konversi;
                $stokDisplay = $bahan->stok.' '.($konversi->satuan_kecil ?? 'unit');

                return (object) [
                    'id' => $bahan->id,
                    'nama' => $bahan->nama,
                    'stok' => $bahan->stok,
                    'min_stok' => $bahan->min_stok,
                    'satuan_kecil' => $konversi->satuan_kecil ?? 'unit',
                    'satuan_besar' => $konversi->satuan_besar ?? 'unit',
                    'konversi_jumlah' => $konversi->jumlah ?? 1,
                    'stok_display' => $stokDisplay,
                    'percentage' => $percentage,
                    'status_color' => $statusColor,
                    'status_text' => $statusText,
                ];
            });

        // 8. RECENT TRANSACTIONS (Today)
        $recentTransactions = Transaksi::with('customer')
            ->whereDate('tgl', $today)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get();

        // 9. SALES CHART DATA - 7 HARI TERAKHIR
        $labels7Hari = [];
        $penjualan7Hari = [];
        $transaksi7Hari = [];
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels7Hari[] = $dayNames[$date->dayOfWeek];
            $penjualan7Hari[] = Transaksi::whereDate('tgl', $date)->sum('bayar') ?? 0;
            $transaksi7Hari[] = Transaksi::whereDate('tgl', $date)->count();
        }

        // 10. SALES CHART DATA - 30 HARI TERAKHIR (per minggu)
        $labels30Hari = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'];
        $penjualan30Hari = [0, 0, 0, 0];
        $transaksi30Hari = [0, 0, 0, 0];

        // Hitung per minggu (7 hari per minggu)
        for ($week = 0; $week < 4; $week++) {
            $startDate = Carbon::today()->subDays((($week + 1) * 7) - 1);
            $endDate = Carbon::today()->subDays($week * 7);

            $penjualan30Hari[$week] = Transaksi::whereBetween('tgl', [$startDate->toDateString(), $endDate->toDateString()])
                ->sum('bayar') ?? 0;
            $transaksi30Hari[$week] = Transaksi::whereBetween('tgl', [$startDate->toDateString(), $endDate->toDateString()])
                ->count();
        }

        // Return data
        return [
            'todaySales' => $todaySales,
            'yesterdaySales' => $yesterdaySales,
            'salesGrowth' => $salesGrowth,
            'todayTransactions' => $todayTransactions,
            'yesterdayTransactions' => $yesterdayTransactions,
            'transactionsGrowth' => $transactionsGrowth,
            'todayProductsSold' => $todayProductsSold,
            'yesterdayProductsSold' => $yesterdayProductsSold,
            'productsSoldGrowth' => $productsSoldGrowth,
            'lowStockCount' => $lowStockCount,
            'lowStockProducts' => $lowStockProducts,
            'lowStockBahanBaku' => $lowStockBahanBaku,
            'topProducts' => $topProducts,
            'recentTransactions' => $recentTransactions,

            // Data chart
            'labels7Hari' => $labels7Hari,
            'penjualan7Hari' => $penjualan7Hari,
            'transaksi7Hari' => $transaksi7Hari,
            'labels30Hari' => $labels30Hari,
            'penjualan30Hari' => $penjualan30Hari,
            'transaksi30Hari' => $transaksi30Hari,
        ];
    }

    /**
     * Export data in various formats
     */
    private function exportData($format)
    {
        $data = $this->getDashboardData();
        $today = Carbon::today()->format('d-m-Y');

        switch ($format) {
            case 'excel':
                return $this->exportExcel($data, $today);
            case 'csv':
                return $this->exportCSV($data, $today);
            case 'pdf':
                return $this->exportPDF($data, $today);
            default:
                abort(404, 'Format export tidak valid');
        }
    }

    /**
     * Export to Excel
     */
    private function exportExcel($data, $date)
    {
        $filename = "dashboard_report_{$date}.xls";

        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        echo '<html>';
        echo '<head>';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; }';
        echo 'th, td { border: 1px solid black; padding: 8px; text-align: left; }';
        echo 'th { background-color: #f2f2f2; }';
        echo '.title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';

        echo "<div class='title'>Laporan Dashboard Sanjaya Bakery - {$date}</div>";

        // Summary Statistics
        echo '<h3>Statistik Ringkasan</h3>';
        echo '<table>';
        echo '<tr><th>Metrik</th><th>Hari Ini</th><th>Pertumbuhan</th></tr>';
        echo '<tr><td>Total Penjualan</td><td>Rp '.number_format($data['todaySales'], 0, ',', '.')."</td><td>{$data['salesGrowth']}%</td></tr>";
        echo "<tr><td>Total Transaksi</td><td>{$data['todayTransactions']}</td><td>{$data['transactionsGrowth']}%</td></tr>";
        echo '<tr><td>Produk Terjual</td><td>'.number_format($data['todayProductsSold'], 0, ',', '.')."</td><td>{$data['productsSoldGrowth']}%</td></tr>";
        echo "<tr><td>Stok Rendah</td><td>{$data['lowStockCount']}</td><td>-</td></tr>";
        echo '</table><br>';

        // Low Stock Products
        if (! empty($data['lowStockProducts'])) {
            echo '<h3>Produk Stok Rendah</h3>';
            echo '<table>';
            echo '<tr><th>Nama Produk</th><th>Stok</th><th>Stok Minimum</th><th>Status</th></tr>';
            foreach ($data['lowStockProducts'] as $product) {
                echo '<tr>';
                echo "<td>{$product->nama}</td>";
                echo "<td>{$product->stok} {$product->satuan}</td>";
                echo "<td>{$product->min_stok} {$product->satuan}</td>";
                echo "<td>{$product->status_text} ({$product->percentage}%)</td>";
                echo '</tr>';
            }
            echo '</table><br>';
        }

        // Low Stock Bahan Baku
        if (! empty($data['lowStockBahanBaku'])) {
            echo '<h3>Bahan Baku Stok Rendah</h3>';
            echo '<table>';
            echo '<tr><th>Nama Bahan Baku</th><th>Stok</th><th>Stok Minimum</th><th>Status</th></tr>';
            foreach ($data['lowStockBahanBaku'] as $bahan) {
                echo '<tr>';
                echo "<td>{$bahan->nama}</td>";
                echo "<td>{$bahan->stok_display}</td>";
                echo "<td>{$bahan->min_stok} {$bahan->satuan_kecil}</td>";
                echo "<td>{$bahan->status_text} ({$bahan->percentage}%)</td>";
                echo '</tr>';
            }
            echo '</table><br>';
        }

        // Top Products
        if (! empty($data['topProducts'])) {
            echo '<h3>Produk Terlaris (7 Hari Terakhir)</h3>';
            echo '<table>';
            echo '<tr><th>Nama Produk</th><th>Harga</th><th>Terjual</th></tr>';
            foreach ($data['topProducts'] as $product) {
                echo '<tr>';
                echo "<td>{$product->nama}</td>";
                echo '<td>Rp '.number_format($product->harga ?? 0, 0, ',', '.').'</td>';
                echo '<td>'.number_format($product->total_qty ?? 0, 0, ',', '.').'</td>';
                echo '</tr>';
            }
            echo '</table><br>';
        }

        // Sales Chart Data (7 days)
        if (! empty($data['penjualan7Hari'])) {
            echo '<h3>Data Penjualan 7 Hari Terakhir</h3>';
            echo '<table>';
            echo '<tr><th>Hari</th><th>Penjualan (Rp)</th></tr>';
            for ($i = 0; $i < count($data['labels7Hari']); $i++) {
                echo '<tr>';
                echo "<td>{$data['labels7Hari'][$i]}</td>";
                echo '<td>Rp '.number_format($data['penjualan7Hari'][$i], 0, ',', '.').'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        echo '</body></html>';
        exit;
    }

    /**
     * Export to CSV
     */
    private function exportCSV($data, $date)
    {
        $filename = "dashboard_report_{$date}.csv";

        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fwrite($output, $bom = (chr(0xEF).chr(0xBB).chr(0xBF)));

        // Summary
        fputcsv($output, ['Laporan Dashboard Sanjaya Bakery - '.$date]);
        fputcsv($output, []);
        fputcsv($output, ['Statistik Ringkasan']);
        fputcsv($output, ['Metrik', 'Hari Ini', 'Pertumbuhan']);
        fputcsv($output, ['Total Penjualan', 'Rp '.number_format($data['todaySales'], 0, ',', '.'), $data['salesGrowth'].'%']);
        fputcsv($output, ['Total Transaksi', $data['todayTransactions'], $data['transactionsGrowth'].'%']);
        fputcsv($output, ['Produk Terjual', number_format($data['todayProductsSold'], 0, ',', '.'), $data['productsSoldGrowth'].'%']);
        fputcsv($output, ['Stok Rendah', $data['lowStockCount'], '-']);
        fputcsv($output, []);

        // Low Stock Products
        if (! empty($data['lowStockProducts'])) {
            fputcsv($output, ['Produk Stok Rendah']);
            fputcsv($output, ['Nama Produk', 'Stok', 'Stok Minimum', 'Status']);
            foreach ($data['lowStockProducts'] as $product) {
                fputcsv($output, [
                    $product->nama,
                    $product->stok.' '.$product->satuan,
                    $product->min_stok.' '.$product->satuan,
                    $product->status_text.' ('.$product->percentage.'%)',
                ]);
            }
            fputcsv($output, []);
        }

        // Low Stock Bahan Baku
        if (! empty($data['lowStockBahanBaku'])) {
            fputcsv($output, ['Bahan Baku Stok Rendah']);
            fputcsv($output, ['Nama Bahan Baku', 'Stok', 'Stok Minimum', 'Status']);
            foreach ($data['lowStockBahanBaku'] as $bahan) {
                fputcsv($output, [
                    $bahan->nama,
                    $bahan->stok_display,
                    $bahan->min_stok.' '.$bahan->satuan_kecil,
                    $bahan->status_text.' ('.$bahan->percentage.'%)',
                ]);
            }
            fputcsv($output, []);
        }

        // Top Products
        if (! empty($data['topProducts'])) {
            fputcsv($output, ['Produk Terlaris (7 Hari Terakhir)']);
            fputcsv($output, ['Nama Produk', 'Harga', 'Terjual']);
            foreach ($data['topProducts'] as $product) {
                fputcsv($output, [
                    $product->nama,
                    'Rp '.number_format($product->harga ?? 0, 0, ',', '.'),
                    number_format($product->total_qty ?? 0, 0, ',', '.'),
                ]);
            }
            fputcsv($output, []);
        }

        // Sales Data
        if (! empty($data['penjualan7Hari'])) {
            fputcsv($output, ['Data Penjualan 7 Hari Terakhir']);
            fputcsv($output, ['Hari', 'Penjualan (Rp)']);
            for ($i = 0; $i < count($data['labels7Hari']); $i++) {
                fputcsv($output, [
                    $data['labels7Hari'][$i],
                    'Rp '.number_format($data['penjualan7Hari'][$i], 0, ',', '.'),
                ]);
            }
        }

        fclose($output);
        exit;
    }

    /**
     * Export to PDF
     */
    private function exportPDF($data, $date)
    {
        // Simple HTML PDF export
        $filename = "dashboard_report_{$date}.html";

        header('Content-Type: application/pdf');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $html = "<!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Dashboard Sanjaya Bakery - {$date}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { text-align: center; color: #2d3748; }
                h3 { color: #4a5568; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th { background-color: #f7fafc; text-align: left; padding: 10px; border: 1px solid #e2e8f0; }
                td { padding: 10px; border: 1px solid #e2e8f0; }
                .summary-box { background-color: #f0fff4; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c6f6d5; }
                .metric { display: inline-block; margin-right: 30px; }
                .metric-value { font-size: 24px; font-weight: bold; color: #2d3748; }
                .metric-label { font-size: 12px; color: #718096; }
                .positive { color: #38a169; }
                .negative { color: #e53e3e; }
            </style>
        </head>
        <body>
            <h1>Laporan Dashboard Sanjaya Bakery</h1>
            <p style='text-align: center; color: #718096;'>Tanggal: {$date}</p>
            
            <div class='summary-box'>
                <h3>Ringkasan Hari Ini</h3>
                <div class='metric'>
                    <div class='metric-value'>Rp ".number_format($data['todaySales'], 0, ',', '.')."</div>
                    <div class='metric-label'>Total Penjualan</div>
                </div>
                <div class='metric'>
                    <div class='metric-value'>{$data['todayTransactions']}</div>
                    <div class='metric-label'>Total Transaksi</div>
                </div>
                <div class='metric'>
                    <div class='metric-value'>".number_format($data['todayProductsSold'], 0, ',', '.')."</div>
                    <div class='metric-label'>Produk Terjual</div>
                </div>
                <div class='metric'>
                    <div class='metric-value'>{$data['lowStockCount']}</div>
                    <div class='metric-label'>Stok Rendah</div>
                </div>
            </div>";

        // Low Stock Products
        if (! empty($data['lowStockProducts'])) {
            $html .= '<h3>Produk Stok Rendah</h3>
            <table>
                <tr>
                    <th>Nama Produk</th>
                    <th>Stok</th>
                    <th>Stok Minimum</th>
                    <th>Status</th>
                </tr>';

            foreach ($data['lowStockProducts'] as $product) {
                $statusColor = $product->status_color == 'red' ? 'style="color: #e53e3e;"' :
                              ($product->status_color == 'orange' ? 'style="color: #ed8936;"' : 'style="color: #d69e2e;"');

                $html .= "<tr>
                    <td>{$product->nama}</td>
                    <td>{$product->stok} {$product->satuan}</td>
                    <td>{$product->min_stok} {$product->satuan}</td>
                    <td {$statusColor}>{$product->status_text} ({$product->percentage}%)</td>
                </tr>";
            }

            $html .= '</table>';
        }

        // Low Stock Bahan Baku
        if (! empty($data['lowStockBahanBaku'])) {
            $html .= '<h3>Bahan Baku Stok Rendah</h3>
            <table>
                <tr>
                    <th>Nama Bahan Baku</th>
                    <th>Stok</th>
                    <th>Stok Minimum</th>
                    <th>Status</th>
                </tr>';

            foreach ($data['lowStockBahanBaku'] as $bahan) {
                $statusColor = $bahan->status_color == 'red' ? 'style="color: #e53e3e;"' :
                              ($bahan->status_color == 'orange' ? 'style="color: #ed8936;"' : 'style="color: #d69e2e;"');

                $html .= "<tr>
                    <td>{$bahan->nama}</td>
                    <td>{$bahan->stok_display}</td>
                    <td>{$bahan->min_stok} {$bahan->satuan_kecil}</td>
                    <td {$statusColor}>{$bahan->status_text} ({$bahan->percentage}%)</td>
                </tr>";
            }

            $html .= '</table>';
        }

        // Top Products
        if (! empty($data['topProducts'])) {
            $html .= '<h3>Produk Terlaris (7 Hari Terakhir)</h3>
            <table>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Terjual</th>
                </tr>';

            foreach ($data['topProducts'] as $product) {
                $html .= "<tr>
                    <td>{$product->nama}</td>
                    <td>Rp ".number_format($product->harga ?? 0, 0, ',', '.').'</td>
                    <td>'.number_format($product->total_qty ?? 0, 0, ',', '.').'</td>
                </tr>';
            }

            $html .= '</table>';
        }

        $html .= '</body></html>';

        echo $html;
        exit;
    }

    /**
     * API endpoint for real-time dashboard data
     */
    public function api()
    {
        $data = $this->getDashboardData();

        return response()->json($data);
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
