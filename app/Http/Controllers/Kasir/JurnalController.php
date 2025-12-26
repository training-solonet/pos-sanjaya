<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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

        // Jika request AJAX, return JSON
        if ($request->ajax() || $request->wantsJson()) {
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
    public function show(string $id, Request $request)
    {
        // Jika ID adalah export, handle export
        if ($id === 'export') {
            return $this->export($request);
        }

        // Default behavior untuk show
        return response()->json(['message' => 'Show not implemented']);
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
     * Export jurnal to PDF or Excel
     */
    public function export(Request $request)
    {
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $format = $request->get('format', 'pdf'); // pdf or excel

        // Get journals for specific date
        $jurnals = Jurnal::whereDate('tgl', $tanggal)
            ->orderBy('tgl', 'desc')
            ->get();

        // Calculate summary
        $totalPemasukan = $jurnals->where('jenis', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $jurnals->where('jenis', 'pengeluaran')->sum('nominal');
        $saldoBersih = $totalPemasukan - $totalPengeluaran;

        $data = [
            'jurnals' => $jurnals,
            'tanggal' => $tanggal,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoBersih' => $saldoBersih,
        ];

        if ($format === 'excel') {
            return $this->exportExcel($data);
        }

        return $this->exportPdf($data);
    }

    private function exportPdf($data)
    {
        $pdf = Pdf::loadView('kasir.jurnal.export-pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('jurnal-kasir-'.$data['tanggal'].'.pdf');
    }

    private function exportExcel($data)
    {
        $filename = 'jurnal-kasir-'.$data['tanggal'].'.xls';
        $view = view('kasir.jurnal.export-excel', $data)->render();
        
        return response($view, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
