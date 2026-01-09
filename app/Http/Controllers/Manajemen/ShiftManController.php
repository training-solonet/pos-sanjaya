<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\Transaksi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShiftManController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cek jika ada parameter export
        if ($request->has('export')) {
            return $this->handleExport($request);
        }

        $period = $request->get('period', 'all');

        // Query shifts yang sudah selesai
        $shiftsQuery = Shift::with(['user'])
            ->whereNotNull('selesai')
            ->orderBy('selesai', 'desc');

        // Filter berdasarkan periode
        switch ($period) {
            case 'today':
                $shiftsQuery->whereDate('selesai', Carbon::today());
                break;
            case 'week':
                $shiftsQuery->whereBetween('selesai', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
                break;
            case 'month':
                $shiftsQuery->whereMonth('selesai', Carbon::now()->month)
                    ->whereYear('selesai', Carbon::now()->year);
                break;
                // 'all' tidak perlu filter tambahan
        }

        // Pagination dengan 10 data per halaman
        $shifts = $shiftsQuery->paginate(10);

        // Hitung statistik untuk setiap shift
        foreach ($shifts as $shift) {
            $statistik = $this->calculateTransactionStatistics($shift->mulai, $shift->selesai, $shift->id_user);
            $shift->total_penjualan = $statistik['total_penjualan'];
            $shift->penjualan_tunai = $statistik['penjualan_tunai'];
            $shift->total_transaksi = $statistik['total_transaksi'];

            // Hitung durasi dengan pembulatan ke menit terdekat
            if ($shift->mulai && $shift->selesai) {
                $start = Carbon::parse($shift->mulai);
                $end = Carbon::parse($shift->selesai);
                $shift->durasi = round($start->diffInMinutes($end)); // Bulatkan ke menit terdekat

                // Format durasi untuk display
                if ($shift->durasi >= 60) {
                    $hours = floor($shift->durasi / 60);
                    $minutes = $shift->durasi % 60;
                    $shift->durasi_formatted = $hours.'j '.$minutes.'m';
                } else {
                    $shift->durasi_formatted = $shift->durasi.'m';
                }
            } else {
                $shift->durasi = 0;
                $shift->durasi_formatted = '0m';
            }
        }

        return view('manajemen.shift.index', compact('shifts', 'period'));
    }

    /**
     * Handle export functionality
     */
    private function handleExport(Request $request)
    {
        $format = $request->get('export', 'excel');
        $period = $request->get('period', 'all');

        // Query shifts yang sudah selesai
        $shiftsQuery = Shift::with(['user'])
            ->whereNotNull('selesai')
            ->orderBy('selesai', 'desc');

        // Filter berdasarkan periode
        switch ($period) {
            case 'today':
                $shiftsQuery->whereDate('selesai', Carbon::today());
                break;
            case 'week':
                $shiftsQuery->whereBetween('selesai', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
                break;
            case 'month':
                $shiftsQuery->whereMonth('selesai', Carbon::now()->month)
                    ->whereYear('selesai', Carbon::now()->year);
                break;
                // 'all' tidak perlu filter tambahan
        }

        $shifts = $shiftsQuery->get();

        // Hitung statistik untuk setiap shift
        foreach ($shifts as $shift) {
            $statistik = $this->calculateTransactionStatistics($shift->mulai, $shift->selesai, $shift->id_user);
            $shift->total_penjualan = $statistik['total_penjualan'];
            $shift->penjualan_tunai = $statistik['penjualan_tunai'];
            $shift->total_transaksi = $statistik['total_transaksi'];

            // Hitung durasi dengan pembulatan ke menit terdekat
            if ($shift->mulai && $shift->selesai) {
                $start = Carbon::parse($shift->mulai);
                $end = Carbon::parse($shift->selesai);
                $shift->durasi = round($start->diffInMinutes($end));

                // Format durasi untuk display
                if ($shift->durasi >= 60) {
                    $hours = floor($shift->durasi / 60);
                    $minutes = $shift->durasi % 60;
                    $shift->durasi_formatted = $hours.'j '.$minutes.'m';
                } else {
                    $shift->durasi_formatted = $shift->durasi.'m';
                }
            } else {
                $shift->durasi = 0;
                $shift->durasi_formatted = '0m';
            }
        }

        // Generate filename
        $filename = $this->generateFilename($period, $format);

        if ($format === 'pdf') {
            return $this->exportPDF($shifts, $period, $filename);
        } elseif ($format === 'csv') {
            return $this->exportCSV($shifts, $period, $filename);
        } else {
            return $this->exportExcel($shifts, $period, $filename);
        }
    }

    private function generateFilename($period, $format)
    {
        $dateString = Carbon::now()->format('Y-m-d_H-i-s');

        return "monitoring_shift_{$period}_{$dateString}.{$format}";
    }

    private function exportPDF($shifts, $period, $filename)
    {
        $dateObj = Carbon::now();
        $periodLabel = '';

        switch ($period) {
            case 'today':
                $periodLabel = 'Hari Ini - '.$dateObj->translatedFormat('d F Y');
                break;
            case 'week':
                $startOfWeek = $dateObj->startOfWeek()->translatedFormat('d F Y');
                $endOfWeek = $dateObj->endOfWeek()->translatedFormat('d F Y');
                $periodLabel = "Minggu Ini - {$startOfWeek} sampai {$endOfWeek}";
                break;
            case 'month':
                $periodLabel = 'Bulan Ini - '.$dateObj->translatedFormat('F Y');
                break;
            default:
                $periodLabel = 'Semua Periode';
        }

        $exportDate = Carbon::now()->translatedFormat('d F Y H:i:s');

        // Buat HTML untuk PDF
        $html = $this->generatePDFHTML($shifts, $periodLabel, $exportDate);

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download($filename);
    }

    private function generatePDFHTML($shifts, $periodLabel, $exportDate)
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Laporan Monitoring Shift</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 11px;
                    margin: 0;
                    padding: 15px;
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
                    font-size: 10px;
                    color: #666;
                }
                
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                    font-size: 9px;
                }
                
                th {
                    background-color: #f5f5f5;
                    border: 1px solid #ddd;
                    padding: 6px;
                    text-align: left;
                    font-weight: bold;
                }
                
                td {
                    border: 1px solid #ddd;
                    padding: 4px;
                }
                
                .text-right {
                    text-align: right;
                }
                
                .text-center {
                    text-align: center;
                }
                
                .bg-blue-100 { background-color: #dbeafe; }
                .bg-green-100 { background-color: #d1fae5; }
                .bg-red-100 { background-color: #fee2e2; }
                .text-blue-700 { color: #1d4ed8; }
                .text-green-700 { color: #047857; }
                .text-red-700 { color: #b91c1c; }
                
                .summary {
                    margin-top: 20px;
                    padding: 15px;
                    background-color: #f9f9f9;
                    border-radius: 5px;
                    font-size: 10px;
                    border: 1px solid #ddd;
                }
                
                .summary h3 {
                    margin-top: 0;
                    margin-bottom: 10px;
                    font-size: 12px;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 5px;
                }
                
                .summary-row {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 5px;
                }
                
                .summary-label {
                    font-weight: bold;
                }
                
                .summary-value {
                    text-align: right;
                }
                
                .footer {
                    margin-top: 30px;
                    text-align: right;
                    font-size: 9px;
                    color: #666;
                }
                
                @media print {
                    body {
                        padding: 0;
                    }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN MONITORING SHIFT</h1>
                <p>Periode: '.$periodLabel.'</p>
                <p>Dibuat pada: '.$exportDate.'</p>
            </div>';

        if ($shifts->count() > 0) {
            $html .= '
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th style="width: 10%">Kasir</th>
                        <th style="width: 12%">Waktu</th>
                        <th style="width: 8%">Durasi</th>
                        <th style="width: 10%" class="text-right">Modal</th>
                        <th style="width: 10%" class="text-right">Penjualan</th>
                        <th style="width: 10%" class="text-right">Tunai</th>
                        <th style="width: 8%">Transaksi</th>
                        <th style="width: 10%" class="text-right">Uang Aktual</th>
                        <th style="width: 10%" class="text-right">Selisih</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($shifts as $shift) {
                $selisihClass = $shift->selisih == 0 ? 'bg-blue-100 text-blue-700' :
                                ($shift->selisih > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700');

                $selisihText = $shift->selisih == 0 ? 'PAS' :
                               ($shift->selisih > 0 ? '+'.number_format($shift->selisih, 0, ',', '.') :
                               '-'.number_format(abs($shift->selisih), 0, ',', '.'));

                // Format waktu
                $waktuMulai = Carbon::parse($shift->mulai)->format('d/m H:i');
                $waktuSelesai = $shift->selesai ? Carbon::parse($shift->selesai)->format('H:i') : '';

                $html .= '
                    <tr>
                        <td>#'.str_pad($shift->id, 6, '0', STR_PAD_LEFT).'</td>
                        <td>'.($shift->user->name ?? 'Unknown').'</td>
                        <td>'.$waktuMulai.($waktuSelesai ? ' - '.$waktuSelesai : '').'</td>
                        <td>'.$shift->durasi_formatted.'</td>
                        <td class="text-right">Rp '.number_format($shift->modal, 0, ',', '.').'</td>
                        <td class="text-right">Rp '.number_format($shift->total_penjualan, 0, ',', '.').'</td>
                        <td class="text-right">Rp '.number_format($shift->penjualan_tunai, 0, ',', '.').'</td>
                        <td class="text-center">'.$shift->total_transaksi.'</td>
                        <td class="text-right">Rp '.number_format($shift->uang_aktual ?? 0, 0, ',', '.').'</td>
                        <td class="text-right '.$selisihClass.'">'.$selisihText.'</td>
                    </tr>';
            }

            $html .= '
                </tbody>
            </table>';

            // Summary
            $totalModal = $shifts->sum('modal');
            $totalPenjualan = $shifts->sum('total_penjualan');
            $totalTunai = $shifts->sum('penjualan_tunai');
            $totalTransaksi = $shifts->sum('total_transaksi');
            $totalSelisih = $shifts->sum('selisih');
            $selisihPositif = $shifts->where('selisih', '>', 0)->sum('selisih');
            $selisihNegatif = $shifts->where('selisih', '<', 0)->sum('selisih');

            $html .= '
            <div class="summary">
                <h3>Ringkasan Total</h3>
                <div class="summary-row">
                    <span class="summary-label">Total Modal:</span>
                    <span class="summary-value">Rp '.number_format($totalModal, 0, ',', '.').'</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Total Penjualan:</span>
                    <span class="summary-value">Rp '.number_format($totalPenjualan, 0, ',', '.').'</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Total Tunai:</span>
                    <span class="summary-value">Rp '.number_format($totalTunai, 0, ',', '.').'</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Total Transaksi:</span>
                    <span class="summary-value">'.$totalTransaksi.' transaksi</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Total Selisih:</span>
                    <span class="summary-value">Rp '.number_format($totalSelisih, 0, ',', '.').'</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Selisih Positif:</span>
                    <span class="summary-value">Rp '.number_format($selisihPositif, 0, ',', '.').'</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Selisih Negatif:</span>
                    <span class="summary-value">Rp '.number_format(abs($selisihNegatif), 0, ',', '.').'</span>
                </div>
            </div>';
        } else {
            $html .= '
            <div style="text-align: center; padding: 40px;">
                <p>Tidak ada data shift untuk periode ini</p>
            </div>';
        }

        $html .= '
            <div class="footer">
                Dicetak oleh: Sistem Monitoring Shift
            </div>
        </body>
        </html>';

        return $html;
    }

    private function exportExcel($shifts, $period, $filename)
    {
        $dateObj = Carbon::now();
        $periodLabel = '';

        switch ($period) {
            case 'today':
                $periodLabel = 'Hari Ini - '.$dateObj->translatedFormat('d F Y');
                break;
            case 'week':
                $startOfWeek = $dateObj->startOfWeek()->translatedFormat('d F Y');
                $endOfWeek = $dateObj->endOfWeek()->translatedFormat('d F Y');
                $periodLabel = "Minggu Ini - {$startOfWeek} sampai {$endOfWeek}";
                break;
            case 'month':
                $periodLabel = 'Bulan Ini - '.$dateObj->translatedFormat('F Y');
                break;
            default:
                $periodLabel = 'Semua Periode';
        }

        $exportDate = Carbon::now()->translatedFormat('d F Y H:i:s');

        // Set headers untuk Excel
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        // Buat konten Excel
        $content = '<html><head><meta charset="UTF-8"><title>Monitoring Shift</title></head><body>';
        $content .= '<h2>LAPORAN MONITORING SHIFT</h2>';
        $content .= '<p><strong>Periode:</strong> '.$periodLabel.'</p>';
        $content .= '<p><strong>Dibuat pada:</strong> '.$exportDate.'</p>';

        if ($shifts->count() > 0) {
            $content .= '<table border="1" cellpadding="5" cellspacing="0">';
            $content .= '<thead><tr>';
            $content .= '<th>ID Shift</th>';
            $content .= '<th>Kasir</th>';
            $content .= '<th>Waktu Mulai</th>';
            $content .= '<th>Waktu Selesai</th>';
            $content .= '<th>Durasi</th>';
            $content .= '<th>Modal (Rp)</th>';
            $content .= '<th>Penjualan (Rp)</th>';
            $content .= '<th>Tunai (Rp)</th>';
            $content .= '<th>Total Transaksi</th>';
            $content .= '<th>Uang Aktual (Rp)</th>';
            $content .= '<th>Selisih (Rp)</th>';
            $content .= '</tr></thead><tbody>';

            foreach ($shifts as $shift) {
                $content .= '<tr>';
                $content .= '<td>#'.str_pad($shift->id, 6, '0', STR_PAD_LEFT).'</td>';
                $content .= '<td>'.($shift->user->name ?? 'Unknown').'</td>';
                $content .= '<td>'.Carbon::parse($shift->mulai)->format('d/m/Y H:i').'</td>';
                $content .= '<td>'.($shift->selesai ? Carbon::parse($shift->selesai)->format('d/m/Y H:i') : '').'</td>';
                $content .= '<td>'.$shift->durasi_formatted.'</td>';
                $content .= '<td>'.number_format($shift->modal, 0, ',', '.').'</td>';
                $content .= '<td>'.number_format($shift->total_penjualan, 0, ',', '.').'</td>';
                $content .= '<td>'.number_format($shift->penjualan_tunai, 0, ',', '.').'</td>';
                $content .= '<td>'.$shift->total_transaksi.'</td>';
                $content .= '<td>'.number_format($shift->uang_aktual ?? 0, 0, ',', '.').'</td>';
                $content .= '<td>'.number_format($shift->selisih, 0, ',', '.').'</td>';
                $content .= '</tr>';
            }

            $content .= '</tbody></table>';

            // Summary
            $totalModal = $shifts->sum('modal');
            $totalPenjualan = $shifts->sum('total_penjualan');
            $totalTunai = $shifts->sum('penjualan_tunai');
            $totalTransaksi = $shifts->sum('total_transaksi');
            $totalSelisih = $shifts->sum('selisih');

            $content .= '<br><br>';
            $content .= '<h3>Ringkasan Total</h3>';
            $content .= '<table border="1" cellpadding="5" cellspacing="0">';
            $content .= '<tr><th>Total Modal</th><td>Rp '.number_format($totalModal, 0, ',', '.').'</td></tr>';
            $content .= '<tr><th>Total Penjualan</th><td>Rp '.number_format($totalPenjualan, 0, ',', '.').'</td></tr>';
            $content .= '<tr><th>Total Tunai</th><td>Rp '.number_format($totalTunai, 0, ',', '.').'</td></tr>';
            $content .= '<tr><th>Total Transaksi</th><td>'.$totalTransaksi.' transaksi</td></tr>';
            $content .= '<tr><th>Total Selisih</th><td>Rp '.number_format($totalSelisih, 0, ',', '.').'</td></tr>';
            $content .= '</table>';
        } else {
            $content .= '<p>Tidak ada data shift untuk periode ini</p>';
        }

        $content .= '</body></html>';

        return response($content, 200, $headers);
    }

    private function exportCSV($shifts, $period, $filename)
    {
        $dateObj = Carbon::now();
        $periodLabel = '';

        switch ($period) {
            case 'today':
                $periodLabel = 'Hari Ini - '.$dateObj->translatedFormat('d F Y');
                break;
            case 'week':
                $startOfWeek = $dateObj->startOfWeek()->translatedFormat('d F Y');
                $endOfWeek = $dateObj->endOfWeek()->translatedFormat('d F Y');
                $periodLabel = "Minggu Ini - {$startOfWeek} sampai {$endOfWeek}";
                break;
            case 'month':
                $periodLabel = 'Bulan Ini - '.$dateObj->translatedFormat('F Y');
                break;
            default:
                $periodLabel = 'Semua Periode';
        }

        $exportDate = Carbon::now()->translatedFormat('d F Y H:i:s');

        // Set headers untuk CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        // Buat konten CSV
        $content = "LAPORAN MONITORING SHIFT\n";
        $content .= 'Periode,'.$periodLabel."\n";
        $content .= 'Dibuat pada,'.$exportDate."\n\n";
        $content .= "ID Shift,Kasir,Waktu Mulai,Waktu Selesai,Durasi,Modal (Rp),Penjualan (Rp),Tunai (Rp),Total Transaksi,Uang Aktual (Rp),Selisih (Rp)\n";

        foreach ($shifts as $shift) {
            $content .= '"#'.str_pad($shift->id, 6, '0', STR_PAD_LEFT).'",';
            $content .= '"'.($shift->user->name ?? 'Unknown').'",';
            $content .= '"'.Carbon::parse($shift->mulai)->format('d/m/Y H:i').'",';
            $content .= '"'.($shift->selesai ? Carbon::parse($shift->selesai)->format('d/m/Y H:i') : '').'",';
            $content .= '"'.$shift->durasi_formatted.'",';
            $content .= number_format($shift->modal, 0, ',', '.').',';
            $content .= number_format($shift->total_penjualan, 0, ',', '.').',';
            $content .= number_format($shift->penjualan_tunai, 0, ',', '.').',';
            $content .= $shift->total_transaksi.',';
            $content .= number_format($shift->uang_aktual ?? 0, 0, ',', '.').',';
            $content .= number_format($shift->selisih, 0, ',', '.')."\n";
        }

        return response($content, 200, $headers);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Cari shift dengan relasi user
            $shift = Shift::with('user')->find($id);

            if (! $shift) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shift tidak ditemukan',
                ], 404);
            }

            // Hitung durasi dengan pembulatan
            if ($shift->mulai && $shift->selesai) {
                $start = Carbon::parse($shift->mulai);
                $end = Carbon::parse($shift->selesai);
                $durasi = round($start->diffInMinutes($end));

                // Format durasi untuk response
                if ($durasi >= 60) {
                    $hours = floor($durasi / 60);
                    $minutes = $durasi % 60;
                    $durasi_formatted = $hours.'j '.$minutes.'m';
                } else {
                    $durasi_formatted = $durasi.'m';
                }
            } else {
                $durasi = 0;
                $durasi_formatted = '0m';
            }

            // Hitung statistik real-time
            $statistik = $this->calculateTransactionStatistics($shift->mulai, $shift->selesai ?: now(), $shift->id_user);

            // Ambil 10 transaksi terbaru
            $transaksis = Transaksi::where('id_user', $shift->id_user)
                ->whereBetween('tgl', [$shift->mulai, $shift->selesai ?: now()])
                ->with('customer')
                ->orderBy('tgl', 'desc')
                ->take(10)
                ->get()
                ->map(function ($transaksi) {
                    return [
                        'id_transaksi' => $transaksi->id_transaksi,
                        'invoice' => $transaksi->id_transaksi,
                        'tgl' => $transaksi->tgl,
                        'metode' => $transaksi->metode,
                        'total' => $transaksi->bayar,
                        'customer' => $transaksi->customer ? $transaksi->customer->nama : 'Non-Member',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'shift' => [
                        'id' => $shift->id,
                        'mulai' => $shift->mulai,
                        'selesai' => $shift->selesai,
                        'modal' => $shift->modal,
                        'total_penjualan' => $statistik['total_penjualan'],
                        'penjualan_tunai' => $statistik['penjualan_tunai'],
                        'total_transaksi' => $statistik['total_transaksi'],
                        'uang_aktual' => $shift->uang_aktual ?? 0,
                        'selisih' => $shift->selisih,
                        'durasi' => $durasi,
                        'durasi_formatted' => $durasi_formatted,
                        'user' => $shift->user ? $shift->user->name : 'Unknown',
                    ],
                    'statistik' => $statistik,
                    'transaksis' => $transaksis,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error in ShiftManController@show: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate transaction statistics
     */
    private function calculateTransactionStatistics($startTime, $endTime, $userId)
    {
        try {
            $transaksis = Transaksi::where('id_user', $userId)
                ->whereBetween('tgl', [$startTime, $endTime])
                ->get();

            $totalPenjualan = 0;
            $penjualanTunai = 0;
            $totalTransaksi = $transaksis->count();

            foreach ($transaksis as $transaksi) {
                $totalPenjualan += $transaksi->bayar ?? 0;

                if (($transaksi->metode ?? '') === 'tunai') {
                    $penjualanTunai += $transaksi->bayar ?? 0;
                }
            }

            return [
                'total_penjualan' => $totalPenjualan,
                'penjualan_tunai' => $penjualanTunai,
                'total_transaksi' => $totalTransaksi,
            ];

        } catch (\Exception $e) {
            Log::error('Error calculating transaction statistics: '.$e->getMessage());

            return [
                'total_penjualan' => 0,
                'penjualan_tunai' => 0,
                'total_transaksi' => 0,
            ];
        }
    }

    /**
     * Methods yang tidak digunakan
     */
    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function edit($id)
    {
        abort(404);
    }

    public function update(Request $request, $id)
    {
        abort(404);
    }

    public function destroy($id)
    {
        abort(404);
    }
}
