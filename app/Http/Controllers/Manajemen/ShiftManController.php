<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftManController extends Controller
{
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
            $shift->penjualan_qris = $statistik['penjualan_qris'];
            $shift->penjualan_kartu = $statistik['penjualan_kartu'];
            $shift->penjualan_transfer = $statistik['penjualan_transfer'];
            $shift->total_transaksi = $statistik['total_transaksi'];

            // PERBAIKAN: Hitung uang seharusnya dan selisih yang benar
            $shift->uang_seharusnya = $shift->modal + $shift->penjualan_tunai;
            $shift->uang_aktual = $shift->uang_aktual ?? 0;
            $shift->selisih = $shift->uang_aktual - $shift->uang_seharusnya;

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

        // Hitung statistik untuk setiap shift menggunakan query yang diperbaiki
        foreach ($shifts as $shift) {
            $statistik = $this->calculateTransactionStatistics($shift->mulai, $shift->selesai, $shift->id_user);
            $shift->total_penjualan = $statistik['total_penjualan'];
            $shift->penjualan_tunai = $statistik['penjualan_tunai'];
            $shift->penjualan_qris = $statistik['penjualan_qris'];
            $shift->penjualan_kartu = $statistik['penjualan_kartu'];
            $shift->penjualan_transfer = $statistik['penjualan_transfer'];
            $shift->total_transaksi = $statistik['total_transaksi'];

            // PERBAIKAN RUMUS: Hitung uang seharusnya yang benar = modal + total penjualan
            $shift->uang_seharusnya = $shift->modal + $shift->total_penjualan;

            // Jika uang_aktual null, set ke 0
            $shift->uang_aktual = $shift->uang_aktual ?? 0;

            // PERBAIKAN: Hitung selisih yang benar
            // Selisih = uang_aktual - (modal + total_penjualan)
            $shift->selisih = $shift->uang_aktual - $shift->uang_seharusnya;

            // Hitung durasi dengan pembulatan ke menit terdekat
            if ($shift->mulai && $shift->selesai) {
                $start = Carbon::parse($shift->mulai);
                $end = Carbon::parse($shift->selesai);
                $shift->durasi = round($start->diffInMinutes($end));

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

            // PERBAIKAN RUMUS: Hitung uang seharusnya dan selisih yang benar
            $uang_seharusnya = $shift->modal + $statistik['total_penjualan'];
            $uang_aktual = $shift->uang_aktual ?? 0;
            $selisih = $uang_aktual - $uang_seharusnya;

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
                        'penjualan_qris' => $statistik['penjualan_qris'],
                        'penjualan_kartu' => $statistik['penjualan_kartu'],
                        'penjualan_transfer' => $statistik['penjualan_transfer'],
                        'total_transaksi' => $statistik['total_transaksi'],
                        'uang_aktual' => $uang_aktual,
                        'uang_seharusnya' => $uang_seharusnya,
                        'selisih' => $selisih,
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
            $stats = DB::table('transaksi')
                ->select(
                    DB::raw('COUNT(*) as total_transaksi'),
                    DB::raw('SUM(bayar) as total_penjualan'),
                    DB::raw('SUM(CASE WHEN metode = "tunai" THEN bayar ELSE 0 END) as penjualan_tunai'),
                    DB::raw('SUM(CASE WHEN metode = "qris" THEN bayar ELSE 0 END) as penjualan_qris'),
                    DB::raw('SUM(CASE WHEN metode = "kartu" THEN bayar ELSE 0 END) as penjualan_kartu'),
                    DB::raw('SUM(CASE WHEN metode = "transfer" THEN bayar ELSE 0 END) as penjualan_transfer')
                )
                ->where('id_user', $userId)
                ->whereBetween('tgl', [$startTime, $endTime])
                ->first();

            if (! $stats) {
                return [
                    'total_penjualan' => 0,
                    'penjualan_tunai' => 0,
                    'penjualan_qris' => 0,
                    'penjualan_kartu' => 0,
                    'penjualan_transfer' => 0,
                    'total_transaksi' => 0,
                ];
            }

            return [
                'total_penjualan' => $stats->total_penjualan ?? 0,
                'penjualan_tunai' => $stats->penjualan_tunai ?? 0,
                'penjualan_qris' => $stats->penjualan_qris ?? 0,
                'penjualan_kartu' => $stats->penjualan_kartu ?? 0,
                'penjualan_transfer' => $stats->penjualan_transfer ?? 0,
                'total_transaksi' => $stats->total_transaksi ?? 0,
            ];

        } catch (\Exception $e) {
            Log::error('Error calculating transaction statistics: '.$e->getMessage());

            return [
                'total_penjualan' => 0,
                'penjualan_tunai' => 0,
                'penjualan_qris' => 0,
                'penjualan_kartu' => 0,
                'penjualan_transfer' => 0,
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
