<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
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

        $shifts = Shift::where('id_user', Auth::id())
            ->whereNotNull('selesai')
            ->orderBy('selesai', 'desc')
            ->limit(50)
            ->get();

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

        // Hitung statistik real-time
        $transaksis = Transaksi::where('id_user', $shift->id_user)
            ->whereBetween('tgl', [$shift->mulai, $shift->selesai ?: now()])
            ->with('detailTransaksis')
            ->get();

        $totalPenjualan = $transaksis->sum(function ($transaksi) {
            return $transaksi->detailTransaksis->sum(function ($detail) {
                return $detail->jumlah * $detail->harga;
            });
        });

        $penjualanTunai = Transaksi::where('id_user', $shift->id_user)
            ->whereBetween('tgl', [$shift->mulai, $shift->selesai ?: now()])
            ->where('metode', 'tunai')
            ->with('detailTransaksis')
            ->get()
            ->sum(function ($transaksi) {
                return $transaksi->detailTransaksis->sum(function ($detail) {
                    return $detail->jumlah * $detail->harga;
                });
            });

        $totalTransaksi = $transaksis->count();

        return response()->json([
            'success' => true,
            'data' => [
                'shift' => $shift,
                'statistik' => [
                    'total_penjualan' => $totalPenjualan,
                    'penjualan_tunai' => $penjualanTunai,
                    'total_transaksi' => $totalTransaksi,
                    'uang_seharusnya' => $shift->modal + $penjualanTunai,
                    'selisih' => $shift->selisih,
                ],
                'transaksis' => $transaksis->take(10),
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

            // Hitung statistik penjualan
            $transaksis = Transaksi::where('id_user', Auth::id())
                ->whereBetween('tgl', [$shift->mulai, $selesai])
                ->with('detailTransaksis')
                ->get();

            $totalPenjualan = $transaksis->sum(function ($transaksi) {
                return $transaksi->detailTransaksis->sum(function ($detail) {
                    return $detail->jumlah * $detail->harga;
                });
            });

            $penjualanTunai = Transaksi::where('id_user', Auth::id())
                ->whereBetween('tgl', [$shift->mulai, $selesai])
                ->where('metode', 'tunai')
                ->with('detailTransaksis')
                ->get()
                ->sum(function ($transaksi) {
                    return $transaksi->detailTransaksis->sum(function ($detail) {
                        return $detail->jumlah * $detail->harga;
                    });
                });

            $totalTransaksi = $transaksis->count();
            $uangSeharusnya = $shift->modal + $penjualanTunai;
            $selisih = $request->uang_aktual - $uangSeharusnya;

            // Update shift
            $shift->update([
                'selesai' => $selesai,
                'durasi' => $durasi,
                'total_penjualan' => $totalPenjualan,
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
     * Get active shift data
     */
    public function getActiveShift()
    {
        $activeShift = Shift::where('id_user', Auth::id())
            ->whereNull('selesai')
            ->first();

        if (! $activeShift) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada shift aktif',
            ], 404);
        }

        // Hitung statistik real-time
        $transaksis = Transaksi::where('id_user', Auth::id())
            ->where('tgl', '>=', $activeShift->mulai)
            ->with('detailTransaksis')
            ->get();

        $totalPenjualan = $transaksis->sum(function ($transaksi) {
            return $transaksi->detailTransaksis->sum(function ($detail) {
                return $detail->jumlah * $detail->harga;
            });
        });

        $penjualanTunai = Transaksi::where('id_user', Auth::id())
            ->where('tgl', '>=', $activeShift->mulai)
            ->where('metode', 'tunai')
            ->with('detailTransaksis')
            ->get()
            ->sum(function ($transaksi) {
                return $transaksi->detailTransaksis->sum(function ($detail) {
                    return $detail->jumlah * $detail->harga;
                });
            });

        $totalTransaksi = $transaksis->count();

        return response()->json([
            'success' => true,
            'data' => [
                'shift' => $activeShift,
                'statistik' => [
                    'total_penjualan' => $totalPenjualan,
                    'penjualan_tunai' => $penjualanTunai,
                    'total_transaksi' => $totalTransaksi,
                ],
            ],
        ]);
    }

    /**
     * Get shift history with period filter
     */
    public function getHistory(Request $request)
    {
        $period = $request->get('period', 'today');
        $query = Shift::where('id_user', Auth::id())
            ->whereNotNull('selesai')
            ->orderBy('selesai', 'desc');

        switch ($period) {
            case 'today':
                $query->whereDate('selesai', today());
                break;
            case 'week':
                $query->whereBetween('selesai', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('selesai', now()->month)
                    ->whereYear('selesai', now()->year);
                break;
                // 'all' tidak ada filter tambahan
        }

        $shifts = $query->limit(100)->get();

        return response()->json([
            'success' => true,
            'data' => $shifts,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        // Tidak ada penghapusan shift
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
