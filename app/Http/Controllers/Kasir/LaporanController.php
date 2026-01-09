<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
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
                'invoice' => $t->id_transaksi,
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
                'invoice' => $t->id_transaksi,
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

    /**
     * Export laporan penjualan ke PDF
     */
    public function exportPDF(Request $request)
    {
        try {
            // Get filter parameters
            $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
            $metode = $request->input('metode');
            $kasir = $request->input('kasir');

            // Query transaksi with relations - include detail transaksi
            $query = Transaksi::with([
                'detailTransaksi.produk.bahanBaku.konversi.satuan',
                'user'
            ])
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

            $transaksi = $query->get();

            // Calculate totals
            $totalPenjualan = $transaksi->sum('bayar');
            $totalTransaksi = $transaksi->count();
            $totalItem = $transaksi->sum(function ($t) {
                return $t->detailTransaksi->sum('jumlah');
            });

            // Group by payment method
            $byPayment = $transaksi->groupBy('metode')->map(function ($items, $method) {
                return [
                    'method' => ucfirst($method),
                    'count' => $items->count(),
                    'total' => $items->sum('bayar'),
                ];
            });

            // Get kasir name if filtered
            $kasirName = null;
            if ($kasir) {
                $kasirUser = User::find($kasir);
                $kasirName = $kasirUser ? $kasirUser->name : null;
            }

            $data = [
                'transaksi' => $transaksi,
                'tanggal' => $tanggal,
                'metode' => $metode,
                'kasirName' => $kasirName,
                'totalPenjualan' => $totalPenjualan,
                'totalTransaksi' => $totalTransaksi,
                'totalItem' => $totalItem,
                'byPayment' => $byPayment,
            ];

            // Debug: tampilkan data jika ada parameter debug
            if ($request->has('debug')) {
                dd($data);
            }

            $pdf = Pdf::loadView('kasir.laporan.pdf', $data);
            $pdf->setPaper('a4', 'landscape');

            $filename = 'Laporan_Detail_Transaksi_'.date('Ymd_His').'.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('Error exporting PDF: '.$e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan saat mengexport PDF: '.$e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Export laporan penjualan ke Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            // Get filter parameters
            $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
            $metode = $request->input('metode');
            $kasir = $request->input('kasir');

            // Query transaksi with relations - include detail transaksi
            $query = Transaksi::with([
                'detailTransaksi.produk.bahanBaku.konversi.satuan',
                'user'
            ])
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

            $transaksi = $query->get();

            // Calculate totals
            $totalPenjualan = $transaksi->sum('bayar');
            $totalTransaksi = $transaksi->count();
            $totalItem = $transaksi->sum(function ($t) {
                return $t->detailTransaksi->sum('jumlah');
            });

            // Group by payment method
            $byPayment = $transaksi->groupBy('metode')->map(function ($items, $method) {
                return [
                    'method' => ucfirst($method),
                    'count' => $items->count(),
                    'total' => $items->sum('bayar'),
                ];
            });

            // Get kasir name if filtered
            $kasirName = null;
            if ($kasir) {
                $kasirUser = User::find($kasir);
                $kasirName = $kasirUser ? $kasirUser->name : null;
            }

            $data = [
                'transaksi' => $transaksi,
                'tanggal' => $tanggal,
                'metode' => $metode,
                'kasirName' => $kasirName,
                'totalPenjualan' => $totalPenjualan,
                'totalTransaksi' => $totalTransaksi,
                'totalItem' => $totalItem,
                'byPayment' => $byPayment,
            ];

            // Debug: tampilkan data jika ada parameter debug
            if ($request->has('debug')) {
                return view('kasir.laporan.excel', $data);
            }

            $filename = 'Laporan_Detail_Transaksi_'.date('Ymd_His').'.xls';

            return response()->view('kasir.laporan.excel', $data)
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        } catch (\Exception $e) {
            \Log::error('Error exporting Excel: '.$e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan saat mengexport Excel: '.$e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
