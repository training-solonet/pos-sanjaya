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
            $totalQty = $details->sum('qty');

            // Format produk list
            $produkList = $details->map(function ($detail) {
                $nama = optional($detail->produk)->nama ?? 'Produk Tidak Ditemukan';

                return $nama.' x'.$detail->qty;
            })->join(', ');

            // Calculate total
            $total = $details->sum(function ($detail) {
                return $detail->qty * $detail->harga;
            });

            return [
                'invoice' => 'INV-'.str_pad($t->id, 5, '0', STR_PAD_LEFT),
                'time' => \Carbon\Carbon::parse($t->tgl)->format('H:i'),
                'products' => $produkList ?: 'Tidak ada produk',
                'quantity' => $totalQty,
                'total' => $total,
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
}
