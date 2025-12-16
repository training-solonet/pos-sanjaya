<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
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
    public function index()
    {
        // Get today's date
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Penjualan Hari Ini (Total from bayar field)
        $penjualanHariIni = Transaksi::whereDate('tgl', $today)->sum('bayar');

        // Penjualan Kemarin
        $penjualanKemarin = Transaksi::whereDate('tgl', $yesterday)->sum('bayar');

        // Hitung persentase perubahan
        $persenPenjualan = $penjualanKemarin > 0
            ? round((($penjualanHariIni - $penjualanKemarin) / $penjualanKemarin) * 100, 1)
            : 0;

        // 2. Total Transaksi Hari Ini
        $totalTransaksiHariIni = Transaksi::whereDate('tgl', $today)->count();

        // Total Transaksi Kemarin
        $totalTransaksiKemarin = Transaksi::whereDate('tgl', $yesterday)->count();

        // Hitung persentase perubahan
        $persenTransaksi = $totalTransaksiKemarin > 0
            ? round((($totalTransaksiHariIni - $totalTransaksiKemarin) / $totalTransaksiKemarin) * 100, 1)
            : 0;

        // 3. Produk Terjual Hari Ini (Total jumlah from detail_transaksi)
        $produkTerjualHariIni = DetailTransaksi::whereHas('transaksi', function ($query) use ($today) {
            $query->whereDate('tgl', $today);
        })->sum('jumlah');

        // Produk Terjual Kemarin
        $produkTerjualKemarin = DetailTransaksi::whereHas('transaksi', function ($query) use ($yesterday) {
            $query->whereDate('tgl', $yesterday);
        })->sum('jumlah');

        // Hitung persentase perubahan
        $persenProdukTerjual = $produkTerjualKemarin > 0
            ? round((($produkTerjualHariIni - $produkTerjualKemarin) / $produkTerjualKemarin) * 100, 1)
            : 0;

        // 4. Stok Rendah (produk dengan stok <= min_stok)
        $stokRendah = Produk::whereRaw('stok <= min_stok')->count();

        // 5. Transaksi Terakhir (3 transaksi terbaru hari ini)
        $transaksiTerakhir = Transaksi::with('detailTransaksi.produk')
            ->whereDate('tgl', $today)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // 6. Produk Terlaris (3 produk dengan penjualan terbanyak hari ini)
        $produkTerlaris = DetailTransaksi::select('id_produk', DB::raw('SUM(jumlah) as total_terjual'))
            ->whereHas('transaksi', function ($query) use ($today) {
                $query->whereDate('tgl', $today);
            })
            ->with('produk')
            ->groupBy('id_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(3)
            ->get();

        // 7. Data untuk grafik penjualan 7 hari terakhir
        $penjualan7Hari = [];
        $transaksi7Hari = [];
        $labels7Hari = [];
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $penjualan7Hari[] = Transaksi::whereDate('tgl', $date)->sum('bayar');
            $transaksi7Hari[] = Transaksi::whereDate('tgl', $date)->count();
            $labels7Hari[] = $dayNames[$date->dayOfWeek];
        }

        return view('kasir.dashboard', compact(
            'penjualanHariIni',
            'persenPenjualan',
            'totalTransaksiHariIni',
            'persenTransaksi',
            'produkTerjualHariIni',
            'persenProdukTerjual',
            'stokRendah',
            'transaksiTerakhir',
            'produkTerlaris',
            'penjualan7Hari',
            'transaksi7Hari',
            'labels7Hari'
        ));
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
