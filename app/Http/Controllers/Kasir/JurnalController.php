<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class JurnalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // Get journals for specific date (sudah include transaksi penjualan yang otomatis tercatat)
        $jurnals = Jurnal::whereDate('tgl', $tanggal)
            ->orderBy('tgl', 'desc')
            ->get();

        // Calculate summary
        $totalPemasukan = Jurnal::whereDate('tgl', $tanggal)
            ->where('jenis', 'pemasukan')
            ->sum('nominal');

        $totalPengeluaran = Jurnal::whereDate('tgl', $tanggal)
            ->where('jenis', 'pengeluaran')
            ->sum('nominal');

        $jumlahPemasukan = Jurnal::whereDate('tgl', $tanggal)
            ->where('jenis', 'pemasukan')
            ->count();

        $jumlahPengeluaran = Jurnal::whereDate('tgl', $tanggal)
            ->where('jenis', 'pengeluaran')
            ->count();

        return view('kasir.jurnal.index', compact(
            'jurnals',
            'tanggal',
            'totalPemasukan',
            'totalPengeluaran',
            'jumlahPemasukan',
            'jumlahPengeluaran'
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
        $validated = $request->validate([
            'tgl' => 'required|date',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required',
            'keterangan' => 'required|string',
            'nominal' => 'required|integer|min:1',
        ]);

        $validated['role'] = 'admin'; // Set role as kasir/admin

        Jurnal::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil ditambahkan',
        ]);
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
        $jurnal = Jurnal::findOrFail($id);

        return response()->json($jurnal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'tgl' => 'required|date',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required',
            'keterangan' => 'required|string',
            'nominal' => 'required|integer|min:1',
        ]);

        $jurnal = Jurnal::findOrFail($id);
        $jurnal->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil diupdate',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jurnal = Jurnal::findOrFail($id);
        $jurnal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus',
        ]);
    }

    /**
     * Get jurnal data for real-time updates (API)
     */
    public function getJurnalData(Request $request)
    {
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // Get journals for specific date
        $jurnals = Jurnal::whereDate('tgl', $tanggal)
            ->orderBy('tgl', 'desc')
            ->get();

        // Calculate summary
        $totalPemasukan = Jurnal::whereDate('tgl', $tanggal)
            ->where('jenis', 'pemasukan')
            ->sum('nominal');

        $totalPengeluaran = Jurnal::whereDate('tgl', $tanggal)
            ->where('jenis', 'pengeluaran')
            ->sum('nominal');

        $jumlahPemasukan = Jurnal::whereDate('tgl', $tanggal)
            ->where('jenis', 'pemasukan')
            ->count();

        $jumlahPengeluaran = Jurnal::whereDate('tgl', $tanggal)
            ->where('jenis', 'pengeluaran')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'jurnals' => $jurnals,
                'totalPemasukan' => $totalPemasukan,
                'totalPengeluaran' => $totalPengeluaran,
                'jumlahPemasukan' => $jumlahPemasukan,
                'jumlahPengeluaran' => $jumlahPengeluaran,
                'saldoBersih' => $totalPemasukan - $totalPengeluaran,
            ],
        ]);
    }

    /**
     * Export jurnal to PDF for specific date
     */
    public function exportPdf(Request $request)
    {
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // Get journals for specific date
        $jurnals = Jurnal::whereDate('tgl', $tanggal)
            ->orderBy('tgl', 'asc')
            ->get();

        // Calculate summary
        $totalPemasukan = $jurnals->where('jenis', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $jurnals->where('jenis', 'pengeluaran')->sum('nominal');
        $jumlahPemasukan = $jurnals->where('jenis', 'pemasukan')->count();
        $jumlahPengeluaran = $jurnals->where('jenis', 'pengeluaran')->count();
        $saldoBersih = $totalPemasukan - $totalPengeluaran;

        // Format tanggal
        $tanggalFormatted = Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y');

        $data = [
            'jurnals' => $jurnals,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'jumlahPemasukan' => $jumlahPemasukan,
            'jumlahPengeluaran' => $jumlahPengeluaran,
            'saldoBersih' => $saldoBersih,
            'tanggal' => $tanggalFormatted,
            'tanggalRaw' => $tanggal,
        ];

        $pdf = Pdf::loadView('kasir.jurnal.export-pdf', $data);
        
        return $pdf->download("Jurnal_Harian_{$tanggal}.pdf");
    }
}
