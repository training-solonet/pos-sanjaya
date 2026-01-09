<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        // Cek jika ada parameter export
        if ($request->has('export')) {
            return $this->handleExport($request);
        }

        // Default filter
        $period = $request->get('period', 'daily');
        $date = $request->get('date', date('Y-m-d'));
        $jenis = $request->get('jenis');
        $kategori = $request->get('kategori');
        $search = $request->get('search');

        // Mulai query untuk data transaksi
        $query = Jurnal::query();

        // Filter berdasarkan periode
        $this->applyPeriodFilter($query, $period, $date);

        // Filter jenis
        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        // Filter kategori
        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        // Filter pencarian
        if ($search) {
            $query->where('keterangan', 'like', '%'.$search.'%');
        }

        // Order by tanggal terbaru
        $query->orderBy('tgl', 'desc')->orderBy('id', 'desc');

        // Pagination 10 data per halaman
        $transactions = $query->paginate(10);

        // Hitung summary berdasarkan filter yang sama TANPA pagination
        $summaryQuery = Jurnal::query();
        $this->applyPeriodFilter($summaryQuery, $period, $date);

        if ($jenis) {
            $summaryQuery->where('jenis', $jenis);
        }

        if ($kategori) {
            $summaryQuery->where('kategori', $kategori);
        }

        if ($search) {
            $summaryQuery->where('keterangan', 'like', '%'.$search.'%');
        }

        // Hitung total summary
        $totalPemasukan = (clone $summaryQuery)->where('jenis', 'pemasukan')->sum('nominal');
        $totalPengeluaran = (clone $summaryQuery)->where('jenis', 'pengeluaran')->sum('nominal');
        $countPemasukan = (clone $summaryQuery)->where('jenis', 'pemasukan')->count();
        $countPengeluaran = (clone $summaryQuery)->where('jenis', 'pengeluaran')->count();

        $summary = [
            'total_revenue' => $totalPemasukan,
            'total_expense' => $totalPengeluaran,
            'revenue_count' => $countPemasukan,
            'expense_count' => $countPengeluaran,
            'net_balance' => $totalPemasukan - $totalPengeluaran,
            'period' => $period,
            'date' => $date,
        ];

        // Tambahkan parameter filter ke pagination links
        $transactions->appends([
            'period' => $period,
            'date' => $date,
            'jenis' => $jenis,
            'kategori' => $kategori,
            'search' => $search,
        ]);

        return view('manajemen.jurnal.index', compact('transactions', 'summary'));
    }

    /**
     * Apply period filter to query
     */
    private function applyPeriodFilter($query, $period, $date)
    {
        if ($period === 'daily') {
            $query->whereDate('tgl', $date);
        } elseif ($period === 'weekly') {
            $startOfWeek = Carbon::parse($date)->startOfWeek();
            $endOfWeek = Carbon::parse($date)->endOfWeek();
            $query->whereBetween('tgl', [$startOfWeek, $endOfWeek]);
        } elseif ($period === 'monthly') {
            $startOfMonth = Carbon::parse($date)->startOfMonth();
            $endOfMonth = Carbon::parse($date)->endOfMonth();
            $query->whereBetween('tgl', [$startOfMonth, $endOfMonth]);
        }

        return $query;
    }

    /**
     * Handle export functionality
     */
    private function handleExport(Request $request)
    {
        $format = $request->get('export', 'excel');
        $period = $request->get('period', 'daily');
        $date = $request->get('date', date('Y-m-d'));
        $jenis = $request->get('jenis');
        $kategori = $request->get('kategori');
        $search = $request->get('search');

        // Mulai query untuk data transaksi
        $query = Jurnal::query();

        // Filter berdasarkan periode
        $this->applyPeriodFilter($query, $period, $date);

        // Filter jenis
        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        // Filter kategori
        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        // Filter pencarian
        if ($search) {
            $query->where('keterangan', 'like', '%'.$search.'%');
        }

        // Order by tanggal terbaru
        $query->orderBy('tgl', 'desc')->orderBy('id', 'desc');

        // Ambil semua data untuk export (tanpa pagination)
        $transactions = $query->get();

        // Hitung summary untuk export
        $summaryQuery = Jurnal::query();
        $this->applyPeriodFilter($summaryQuery, $period, $date);

        if ($jenis) {
            $summaryQuery->where('jenis', $jenis);
        }

        if ($kategori) {
            $summaryQuery->where('kategori', $kategori);
        }

        if ($search) {
            $summaryQuery->where('keterangan', 'like', '%'.$search.'%');
        }

        $totalPemasukan = (clone $summaryQuery)->where('jenis', 'pemasukan')->sum('nominal');
        $totalPengeluaran = (clone $summaryQuery)->where('jenis', 'pengeluaran')->sum('nominal');
        $countPemasukan = (clone $summaryQuery)->where('jenis', 'pemasukan')->count();
        $countPengeluaran = (clone $summaryQuery)->where('jenis', 'pengeluaran')->count();

        $summary = [
            'total_revenue' => $totalPemasukan,
            'total_expense' => $totalPengeluaran,
            'revenue_count' => $countPemasukan,
            'expense_count' => $countPengeluaran,
            'net_balance' => $totalPemasukan - $totalPengeluaran,
            'period' => $period,
            'date' => $date,
        ];

        // Generate filename
        $filename = $this->generateFilename($period, $date, $format);

        if ($format === 'pdf') {
            return $this->exportPDF($transactions, $summary, $period, $date, $filename);
        } elseif ($format === 'csv') {
            return $this->exportCSV($transactions, $summary, $period, $date, $filename);
        } else {
            return $this->exportExcel($transactions, $summary, $period, $date, $filename);
        }
    }

    private function generateFilename($period, $date, $format)
    {
        $dateObj = Carbon::parse($date);

        switch ($period) {
            case 'daily':
                $dateString = $dateObj->format('Y-m-d');
                break;
            case 'weekly':
                $startOfWeek = $dateObj->startOfWeek()->format('Y-m-d');
                $endOfWeek = $dateObj->endOfWeek()->format('Y-m-d');
                $dateString = "{$startOfWeek}_to_{$endOfWeek}";
                break;
            case 'monthly':
                $dateString = $dateObj->format('Y-m');
                break;
            default:
                $dateString = $dateObj->format('Y-m-d');
        }

        return "jurnal_{$period}_{$dateString}.{$format}";
    }

    private function exportPDF($transactions, $summary, $period, $date, $filename)
    {
        $dateObj = Carbon::parse($date);
        $periodLabel = '';

        switch ($period) {
            case 'daily':
                $periodLabel = 'Harian - '.$dateObj->translatedFormat('d F Y');
                break;
            case 'weekly':
                $startOfWeek = $dateObj->startOfWeek()->translatedFormat('d F Y');
                $endOfWeek = $dateObj->endOfWeek()->translatedFormat('d F Y');
                $periodLabel = "Mingguan - {$startOfWeek} sampai {$endOfWeek}";
                break;
            case 'monthly':
                $periodLabel = 'Bulanan - '.$dateObj->translatedFormat('F Y');
                break;
        }

        $exportDate = Carbon::now()->translatedFormat('d F Y H:i:s');

        // Buat HTML untuk PDF
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Jurnal Transaksi</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }
                
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 10px;
                }
                
                .header h1 {
                    font-size: 16px;
                    margin: 0;
                    font-weight: bold;
                }
                
                .header p {
                    margin: 5px 0;
                    font-size: 11px;
                    color: #666;
                }
                
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                
                th {
                    background-color: #f5f5f5;
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                    font-weight: bold;
                    font-size: 11px;
                }
                
                td {
                    border: 1px solid #ddd;
                    padding: 6px;
                    font-size: 10px;
                }
                
                .text-right {
                    text-align: right;
                }
                
                .text-center {
                    text-align: center;
                }
                
                .pemasukan {
                    color: #10b981;
                }
                
                .pengeluaran {
                    color: #ef4444;
                }
                
                .summary {
                    margin-top: 20px;
                    width: 100%;
                    border-collapse: collapse;
                }
                
                .summary td {
                    border: none;
                    padding: 5px 0;
                    font-size: 11px;
                }
                
                .summary-label {
                    font-weight: bold;
                    padding-right: 20px;
                }
                
                .summary-value {
                    text-align: right;
                    font-weight: bold;
                }
                
                .footer {
                    margin-top: 30px;
                    text-align: right;
                    font-size: 10px;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN JURNAL TRANSAKSI</h1>
                <p>Periode: '.$periodLabel.'</p>
                <p>Dibuat pada: '.$exportDate.'</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%">Tanggal</th>
                        <th style="width: 15%">Jenis</th>
                        <th style="width: 15%">Kategori</th>
                        <th style="width: 35%">Keterangan</th>
                        <th style="width: 20%" class="text-right">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody>';

        if ($transactions->count() > 0) {
            foreach ($transactions as $transaction) {
                $jenis = $transaction->jenis === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran';
                $class = $transaction->jenis === 'pemasukan' ? 'pemasukan' : 'pengeluaran';
                $tgl = Carbon::parse($transaction->tgl)->format('d/m/Y');
                $nominal = number_format($transaction->nominal, 0, ',', '.');
                $prefix = $transaction->jenis === 'pemasukan' ? '+' : '-';

                $html .= '
                    <tr>
                        <td>'.$tgl.'</td>
                        <td><span class="'.$class.'">'.$jenis.'</span></td>
                        <td>'.$transaction->kategori.'</td>
                        <td>'.$transaction->keterangan.'</td>
                        <td class="text-right">'.$prefix.' '.$nominal.'</td>
                    </tr>';
            }
        } else {
            $html .= '
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data transaksi</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
            
            <table class="summary">
                <tr>
                    <td class="summary-label">Total Pemasukan:</td>
                    <td class="summary-value pemasukan">Rp '.number_format($summary['total_revenue'], 0, ',', '.').'</td>
                </tr>
                <tr>
                    <td class="summary-label">Total Pengeluaran:</td>
                    <td class="summary-value pengeluaran">Rp '.number_format($summary['total_expense'], 0, ',', '.').'</td>
                </tr>
                <tr>
                    <td class="summary-label">Saldo Bersih:</td>
                    <td class="summary-value" style="color: '.($summary['net_balance'] > 0 ? '#10b981' : '#ef4444').'">
                        Rp '.number_format($summary['net_balance'], 0, ',', '.').'
                    </td>
                </tr>
            </table>
            
            <div class="footer">
                Dicetak oleh: Sistem Manajemen
            </div>
        </body>
        </html>';

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($filename);
    }

    private function exportExcel($transactions, $summary, $period, $date, $filename)
    {
        $dateObj = Carbon::parse($date);
        $periodLabel = '';

        switch ($period) {
            case 'daily':
                $periodLabel = 'Harian - '.$dateObj->translatedFormat('d F Y');
                break;
            case 'weekly':
                $startOfWeek = $dateObj->startOfWeek()->translatedFormat('d F Y');
                $endOfWeek = $dateObj->endOfWeek()->translatedFormat('d F Y');
                $periodLabel = "Mingguan - {$startOfWeek} sampai {$endOfWeek}";
                break;
            case 'monthly':
                $periodLabel = 'Bulanan - '.$dateObj->translatedFormat('F Y');
                break;
        }

        $exportDate = Carbon::now()->translatedFormat('d F Y H:i:s');

        // Set headers untuk Excel
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        // Buat konten Excel (CSV format untuk Excel)
        $content = "LAPORAN JURNAL TRANSAKSI\n";
        $content .= 'Periode: '.$periodLabel."\n";
        $content .= 'Dibuat pada: '.$exportDate."\n\n";
        $content .= "Tanggal;Jenis;Kategori;Keterangan;Nominal (Rp)\n";

        foreach ($transactions as $transaction) {
            $jenis = $transaction->jenis === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran';
            $tgl = Carbon::parse($transaction->tgl)->format('d/m/Y');
            $nominal = number_format($transaction->nominal, 0, ',', '.');

            $content .= "{$tgl};{$jenis};{$transaction->kategori};{$transaction->keterangan};{$nominal}\n";
        }

        $content .= "\n";
        $content .= 'Total Pemasukan;'.number_format($summary['total_revenue'], 0, ',', '.')."\n";
        $content .= 'Total Pengeluaran;'.number_format($summary['total_expense'], 0, ',', '.')."\n";
        $content .= 'Saldo Bersih;'.number_format($summary['net_balance'], 0, ',', '.')."\n";

        return Response::make($content, 200, $headers);
    }

    private function exportCSV($transactions, $summary, $period, $date, $filename)
    {
        $dateObj = Carbon::parse($date);
        $periodLabel = '';

        switch ($period) {
            case 'daily':
                $periodLabel = 'Harian - '.$dateObj->translatedFormat('d F Y');
                break;
            case 'weekly':
                $startOfWeek = $dateObj->startOfWeek()->translatedFormat('d F Y');
                $endOfWeek = $dateObj->endOfWeek()->translatedFormat('d F Y');
                $periodLabel = "Mingguan - {$startOfWeek} sampai {$endOfWeek}";
                break;
            case 'monthly':
                $periodLabel = 'Bulanan - '.$dateObj->translatedFormat('F Y');
                break;
        }

        $exportDate = Carbon::now()->translatedFormat('d F Y H:i:s');

        // Set headers untuk CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        // Buat konten CSV
        $content = "LAPORAN JURNAL TRANSAKSI\n";
        $content .= "Periode,{$periodLabel}\n";
        $content .= "Dibuat pada,{$exportDate}\n\n";
        $content .= "Tanggal,Jenis,Kategori,Keterangan,Nominal (Rp)\n";

        foreach ($transactions as $transaction) {
            $jenis = $transaction->jenis === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran';
            $tgl = Carbon::parse($transaction->tgl)->format('d/m/Y');
            $nominal = number_format($transaction->nominal, 0, ',', '.');

            $content .= "{$tgl},{$jenis},{$transaction->kategori},\"{$transaction->keterangan}\",{$nominal}\n";
        }

        $content .= "\n";
        $content .= 'Total Pemasukan,'.number_format($summary['total_revenue'], 0, ',', '.')."\n";
        $content .= 'Total Pengeluaran,'.number_format($summary['total_expense'], 0, ',', '.')."\n";
        $content .= 'Saldo Bersih,'.number_format($summary['net_balance'], 0, ',', '.')."\n";

        return Response::make($content, 200, $headers);
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

    public function create() {}

    public function edit($id) {}
}
