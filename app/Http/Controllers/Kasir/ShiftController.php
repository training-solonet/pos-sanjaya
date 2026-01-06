<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use App\Models\Shift;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $activeShift = Shift::where('id_user', Auth::id())
            ->whereNull('selesai')
            ->first();

        // Jika ada shift aktif, hitung statistik real-time dari JURNAL
        if ($activeShift) {
            $statistik = $this->calculateTransactionStatistics($activeShift->mulai, now());

            // Tambahkan statistik sebagai property ke objek activeShift
            $activeShift->total_penjualan_calculated = $statistik['total_penjualan'];
            $activeShift->penjualan_tunai_calculated = $statistik['penjualan_tunai'];
            $activeShift->total_transaksi_calculated = $statistik['total_transaksi'];

            // Juga simpan data statistik ke dalam atribut shift untuk view
            $activeShift->total_penjualan = $statistik['total_penjualan'];
            $activeShift->penjualan_tunai = $statistik['penjualan_tunai'];
            $activeShift->total_transaksi = $statistik['total_transaksi'];
        }

        $period = $request->get('period', 'today');
        $shiftsQuery = Shift::where('id_user', Auth::id())
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

        $shifts = $shiftsQuery->limit(50)->get();

        return view('kasir.shift.index', compact('activeShift', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'modal' => 'required|numeric|min:0',
        ]);

        // Cek apakah ada shift aktif
        $activeShift = Shift::where('id_user', Auth::id())
            ->whereNull('selesai')
            ->first();

        if ($activeShift) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada shift aktif yang belum ditutup!',
            ], 400);
        }

        DB::beginTransaction();
        try {
            $shift = Shift::create([
                'id_user' => Auth::id(),
                'mulai' => now(),
                'modal' => $request->modal,
                'total_penjualan' => 0,
                'penjualan_tunai' => 0,
                'total_transaksi' => 0,
                'selisih' => 0,
                'durasi' => 0,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shift berhasil dimulai!',
                'data' => $shift,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai shift: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        // Pastikan shift milik user yang login
        if ($shift->id_user != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak',
            ], 403);
        }

        // Hitung statistik real-time DARI JURNAL (lebih akurat)
        $statistik = $this->calculateTransactionStatistics($shift->mulai, $shift->selesai ?: now());

        // Ambil 10 transaksi terbaru dari jurnal untuk menampilkan detail
        $jurnals = Jurnal::whereDate('tgl', '>=', $shift->mulai)
            ->whereDate('tgl', '<=', $shift->selesai ?: now())
            ->where('kategori', 'Penjualan')
            ->where('jenis', 'pemasukan')
            ->orderBy('tgl', 'desc')
            ->take(10)
            ->get()
            ->map(function ($jurnal) {
                // Parse invoice number dan metode dari keterangan
                preg_match('/Invoice INV-(\d+) \((.+?)\)/', $jurnal->keterangan, $matches);
                $invoiceNumber = isset($matches[1]) ? $matches[1] : null;
                $metode = isset($matches[2]) ? $matches[2] : 'unknown';

                return [
                    'invoice' => $invoiceNumber ? 'INV-'.str_pad($invoiceNumber, 5, '0', STR_PAD_LEFT) : '-',
                    'keterangan' => $jurnal->keterangan,
                    'tgl' => $jurnal->tgl,
                    'metode' => $metode,
                    'total' => $jurnal->nominal,
                ];
            });

        // Hitung transaksi dari database transaksi untuk jumlah (lebih cepat)
        $totalTransaksiFromTransaksi = Transaksi::where('id_user', $shift->id_user)
            ->whereBetween('tgl', [$shift->mulai, $shift->selesai ?: now()])
            ->count();

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
                    'total_transaksi' => $totalTransaksiFromTransaksi,
                    'uang_aktual' => $shift->uang_aktual ?? 0,
                    'selisih' => $shift->selisih,
                    'durasi' => $shift->durasi,
                ],
                'statistik' => $statistik,
                'transaksis' => $jurnals,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shift $shift)
    {
        // Pastikan shift milik user yang login dan masih aktif
        if ($shift->id_user != Auth::id() || $shift->selesai !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Shift sudah ditutup atau bukan milik Anda',
            ], 403);
        }

        $request->validate([
            'uang_aktual' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Hitung durasi
            $mulai = Carbon::parse($shift->mulai);
            $selesai = now();
            $durasi = $mulai->diffInMinutes($selesai);

            // Hitung statistik penjualan terakhir DARI JURNAL
            $statistik = $this->calculateTransactionStatistics($shift->mulai, $selesai);

            // Hitung total transaksi dari tabel transaksi
            $totalTransaksiFromTransaksi = Transaksi::where('id_user', Auth::id())
                ->whereBetween('tgl', [$shift->mulai, $selesai])
                ->count();

            // Hitung selisih
            $uangSeharusnya = $shift->modal + $statistik['penjualan_tunai'];
            $selisih = $request->uang_aktual - $uangSeharusnya;

            // Update shift
            $shift->update([
                'selesai' => $selesai,
                'durasi' => $durasi,
                'total_penjualan' => $statistik['total_penjualan'],
                'penjualan_tunai' => $statistik['penjualan_tunai'],
                'total_transaksi' => $totalTransaksiFromTransaksi,
                'uang_aktual' => $request->uang_aktual,
                'selisih' => $selisih,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shift berhasil ditutup!',
                'data' => $shift,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup shift: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get active shift statistics
     */
    public function getActiveStats(Shift $shift)
    {
        if ($shift->id_user != Auth::id() || $shift->selesai !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak',
            ], 403);
        }

        $statistik = $this->calculateTransactionStatistics($shift->mulai, now());

        return response()->json([
            'success' => true,
            'data' => [
                'shift' => [
                    'id' => $shift->id,
                    'modal' => $shift->modal,
                ],
                'statistik' => $statistik,
            ],
        ]);
    }

    /**
     * Calculate transaction statistics for a shift using JURNAL data (more accurate)
     */
    private function calculateTransactionStatistics($startTime, $endTime)
    {
        // Hitung dari JURNAL karena data lebih akurat dan sudah include semua metode pembayaran
        $jurnals = Jurnal::where('kategori', 'Penjualan')
            ->where('jenis', 'pemasukan')
            ->whereBetween('tgl', [$startTime, $endTime])
            ->get();

        $totalPenjualan = 0;
        $penjualanTunai = 0;

        foreach ($jurnals as $jurnal) {
            $totalPenjualan += $jurnal->nominal;

            // Cek jika metode pembayaran adalah tunai dari keterangan
            if (strpos($jurnal->keterangan, '(tunai)') !== false) {
                $penjualanTunai += $jurnal->nominal;
            }
        }

        // Hitung total transaksi dari tabel transaksi (lebih cepat untuk count)
        $totalTransaksi = Transaksi::where('id_user', Auth::id())
            ->whereBetween('tgl', [$startTime, $endTime])
            ->count();

        return [
            'total_penjualan' => $totalPenjualan,
            'penjualan_tunai' => $penjualanTunai,
            'total_transaksi' => $totalTransaksi,
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        return response()->json([
            'success' => false,
            'message' => 'Fitur ini tidak tersedia',
        ], 400);
    }

    /**
     * Show the form for creating/editing (tidak digunakan)
     */
    public function create()
    {
        abort(404);
    }

    public function edit(Shift $shift)
    {
        abort(404);
    }
}
