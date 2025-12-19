<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
        $metode = $request->input('metode');
        $kasir = $request->input('kasir');
        $search = $request->input('search');

        // Query transaksi with relations
        $query = Transaksi::with(['detailTransaksi.produk', 'user'])
            ->orderBy('tgl', 'desc');

        // Apply filters
        if ($tanggal) {
            $query->whereDate('tgl', $tanggal);
        }

        if ($metode) {
            $query->where('metode', $metode);
        }

        if ($kasir) {
            $query->where('id_user', $kasir);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%'.$search.'%');
            });
        }

        $transaksi = $query->get();

        // Get all kasir for filter
        $kasirList = User::where('role', 'kasir')->get();

        // Transform data untuk view
        $transactions = $transaksi->map(function ($t) {
            $details = $t->detailTransaksi;
            $totalQty = $details->sum('jumlah'); // Kolom di database adalah 'jumlah'

            // Format produk list
            $produkList = $details->map(function ($detail) {
                $nama = optional($detail->produk)->nama ?? 'Produk Tidak Ditemukan';

                return $nama.' x'.$detail->jumlah; // Kolom di database adalah 'jumlah'
            })->join(', ');

            return [
                'invoice' => 'INV-'.str_pad($t->id, 5, '0', STR_PAD_LEFT),
                'time' => \Carbon\Carbon::parse($t->tgl)->format('H:i'),
                'products' => $produkList ?: 'Tidak ada produk',
                'quantity' => $totalQty,
                'total' => $t->bayar, // Ambil dari kolom bayar di tabel transaksi
                'payment' => ucfirst($t->metode),
                'cashier' => optional($t->user)->name ?? 'Unknown',
            ];
        });

        return view('kasir.laporan.index', compact('transactions', 'kasirList'));
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

    /**
     * Get transactions data for real-time updates (API endpoint)
     */
    public function getTransactions(Request $request)
    {
        // Get filter parameters
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
        $metode = $request->input('metode');
        $kasir = $request->input('kasir');
        $search = $request->input('search');

        // Query transaksi with relations
        $query = Transaksi::with(['detailTransaksi.produk', 'user'])
            ->orderBy('tgl', 'desc');

        // Apply filters
        if ($tanggal) {
            $query->whereDate('tgl', $tanggal);
        }

        if ($metode) {
            $query->where('metode', $metode);
        }

        if ($kasir) {
            $query->where('id_user', $kasir);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%'.$search.'%');
            });
        }

        $transaksi = $query->get();

        // Transform data untuk JSON response
        $transactions = $transaksi->map(function ($t) {
            $details = $t->detailTransaksi;
            $totalQty = $details->sum('jumlah'); // Kolom di database adalah 'jumlah'

            // Format produk list
            $produkList = $details->map(function ($detail) {
                $nama = optional($detail->produk)->nama ?? 'Produk Tidak Ditemukan';

                return $nama.' x'.$detail->jumlah; // Kolom di database adalah 'jumlah'
            })->join(', ');

            return [
                'invoice' => 'INV-'.str_pad($t->id, 5, '0', STR_PAD_LEFT),
                'time' => \Carbon\Carbon::parse($t->tgl)->format('H:i'),
                'products' => $produkList ?: 'Tidak ada produk',
                'quantity' => $totalQty,
                'total' => $t->bayar, // Ambil dari kolom bayar di tabel transaksi
                'payment' => ucfirst($t->metode),
                'cashier' => optional($t->user)->name ?? 'Unknown',
            ];
        });

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
        ]);
    }
}
