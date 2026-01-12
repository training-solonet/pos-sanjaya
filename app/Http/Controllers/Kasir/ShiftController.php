<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function index()
    {
        $activeShift = Shift::where('id_user', Auth::id())
            ->whereNull('selesai')
            ->first();

        // Hitung statistik untuk shift aktif
        if ($activeShift) {
            $stats = $this->calculateActiveShiftStats($activeShift);
            $activeShift->total_penjualan_calculated = $stats['total_penjualan'];
            $activeShift->penjualan_tunai_calculated = $stats['penjualan_tunai'];
            $activeShift->penjualan_qris_calculated = $stats['penjualan_qris'];
            $activeShift->penjualan_kartu_calculated = $stats['penjualan_kartu'];
            $activeShift->penjualan_transfer_calculated = $stats['penjualan_transfer'];
            $activeShift->total_transaksi_calculated = $stats['total_transaksi'];
        }

        $shifts = Shift::where('id_user', Auth::id())
            ->whereNotNull('selesai')
            ->orderBy('selesai', 'desc')
            ->paginate(10);

        return view('kasir.shift.index', compact('activeShift', 'shifts'));
    }

    private function calculateActiveShiftStats($shift)
    {
        $transactions = Transaksi::where('id_user', $shift->id_user)
            ->whereBetween('tgl', [$shift->mulai, now()])
            ->get();

        $total_penjualan = $transactions->sum('bayar');
        $total_transaksi = $transactions->count();

        // Hitung berdasarkan metode pembayaran
        $penjualan_tunai = $transactions->where('metode', 'tunai')->sum('bayar');
        $penjualan_qris = $transactions->where('metode', 'qris')->sum('bayar');
        $penjualan_kartu = $transactions->where('metode', 'kartu')->sum('bayar');
        $penjualan_transfer = $transactions->where('metode', 'transfer')->sum('bayar');

        return [
            'total_penjualan' => $total_penjualan,
            'penjualan_tunai' => $penjualan_tunai,
            'penjualan_qris' => $penjualan_qris,
            'penjualan_kartu' => $penjualan_kartu,
            'penjualan_transfer' => $penjualan_transfer,
            'total_transaksi' => $total_transaksi,
        ];
    }

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
                'message' => 'Masih ada shift aktif',
            ], 400);
        }

        $shift = Shift::create([
            'id_user' => Auth::id(),
            'mulai' => now(),
            'modal' => $request->modal,
            'uang_aktual' => $request->modal,
            'selisih' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift berhasil dimulai',
            'data' => $shift,
        ]);
    }

    public function show($id)
    {
        $shift = Shift::findOrFail($id);

        // Hitung statistik real-time
        $transactions = Transaksi::where('id_user', $shift->id_user)
            ->whereBetween('tgl', [$shift->mulai, $shift->selesai ?? now()])
            ->get();

        $stats = [
            'total_penjualan' => $transactions->sum('bayar'),
            'penjualan_tunai' => $transactions->where('metode', 'tunai')->sum('bayar'),
            'penjualan_qris' => $transactions->where('metode', 'qris')->sum('bayar'),
            'penjualan_kartu' => $transactions->where('metode', 'kartu')->sum('bayar'),
            'penjualan_transfer' => $transactions->where('metode', 'transfer')->sum('bayar'),
            'total_transaksi' => $transactions->count(),
        ];

        // Ambil 10 transaksi terbaru
        $transaksis = Transaksi::where('id_user', $shift->id_user)
            ->whereBetween('tgl', [$shift->mulai, $shift->selesai ?? now()])
            ->with('customer')
            ->orderBy('tgl', 'desc')
            ->take(10)
            ->get()
            ->map(function ($transaksi) {
                return [
                    'id_transaksi' => $transaksi->id_transaksi,
                    'tgl' => $transaksi->tgl,
                    'metode' => $transaksi->metode,
                    'total' => $transaksi->bayar,
                    'customer' => $transaksi->customer ? $transaksi->customer->nama : 'Non-Member',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'shift' => $shift,
                'statistik' => $stats,
                'transaksis' => $transaksis,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'uang_aktual' => 'required|numeric|min:0',
        ]);

        $shift = Shift::findOrFail($id);

        // PERBAIKAN RUMUS: Hitung total penjualan (semua metode)
        $total_penjualan = Transaksi::where('id_user', $shift->id_user)
            ->whereBetween('tgl', [$shift->mulai, now()])
            ->sum('bayar');

        // PERBAIKAN RUMUS: Uang Seharusnya = Modal Awal + Total Penjualan (semua metode)
        $uang_seharusnya = $shift->modal + $total_penjualan;
        $selisih = $request->uang_aktual - $uang_seharusnya;

        $shift->update([
            'selesai' => now(),
            'uang_aktual' => $request->uang_aktual,
            'selisih' => $selisih,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shift berhasil ditutup',
            'data' => [
                'selisih' => $selisih,
                'uang_seharusnya' => $uang_seharusnya,
            ],
        ]);
    }
}
