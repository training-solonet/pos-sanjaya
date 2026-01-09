<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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
            ->paginate(10)
            ->appends(['tanggal' => $tanggal]);

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
        try {
            $tanggal = $request->get('tanggal', date('Y-m-d'));

            // Get journals for specific date dari database sanjaya tabel jurnal
            $jurnals = Jurnal::whereDate('tgl', $tanggal)
                ->orderBy('tgl', 'asc')
                ->get();

            \Log::info('Export PDF - Tanggal: '.$tanggal.', Jumlah data: '.$jurnals->count());

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

            $pdf = Pdf::loadView('kasir.jurnal.export-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('margin-top', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('margin-left', 10);

            return $pdf->download("Jurnal_Harian_{$tanggal}.pdf");
        } catch (\Exception $e) {
            \Log::error('Export PDF Error: '.$e->getMessage());

            return back()->with('error', 'Gagal generate PDF: '.$e->getMessage());
        }
    }

    /**
     * Export jurnal to Excel for specific date
     */
    public function exportExcel(Request $request)
    {
        try {
            $tanggal = $request->get('tanggal', date('Y-m-d'));

            // Get journals for specific date dari database sanjaya tabel jurnal
            $jurnals = Jurnal::whereDate('tgl', $tanggal)
                ->orderBy('tgl', 'asc')
                ->get();

            \Log::info('Export Excel - Tanggal: '.$tanggal.', Jumlah data: '.$jurnals->count());

            // Calculate summary
            $totalPemasukan = $jurnals->where('jenis', 'pemasukan')->sum('nominal');
            $totalPengeluaran = $jurnals->where('jenis', 'pengeluaran')->sum('nominal');
            $saldoBersih = $totalPemasukan - $totalPengeluaran;

            // Format tanggal
            $tanggalFormatted = Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y');
            $tanggalFile = Carbon::parse($tanggal)->format('d-m-Y');

            // Create CSV/Excel content
            $fileName = "Jurnal_Harian_{$tanggalFile}.csv";
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            $callback = function () use ($jurnals, $totalPemasukan, $totalPengeluaran, $saldoBersih, $tanggalFormatted) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Title
                fputcsv($file, ['LAPORAN JURNAL HARIAN']);
                fputcsv($file, ['Tanggal: '.$tanggalFormatted]);
                fputcsv($file, []); // Empty row

                // Header
                fputcsv($file, ['No', 'Tanggal', 'Jenis', 'Kategori', 'Keterangan', 'Nominal']);

                // Data
                if ($jurnals->count() > 0) {
                    $no = 1;
                    foreach ($jurnals as $jurnal) {
                        fputcsv($file, [
                            $no++,
                            Carbon::parse($jurnal->tgl)->format('d/m/Y'),
                            ucfirst($jurnal->jenis),
                            $jurnal->kategori,
                            $jurnal->keterangan,
                            'Rp '.number_format($jurnal->nominal, 0, ',', '.'),
                        ]);
                    }
                } else {
                    fputcsv($file, ['Tidak ada data transaksi untuk tanggal ini']);
                }

                // Summary
                fputcsv($file, []); // Empty row
                fputcsv($file, ['', '', '', '', 'Total Pemasukan:', 'Rp '.number_format($totalPemasukan, 0, ',', '.')]);
                fputcsv($file, ['', '', '', '', 'Total Pengeluaran:', 'Rp '.number_format($totalPengeluaran, 0, ',', '.')]);
                fputcsv($file, ['', '', '', '', 'Saldo Bersih:', 'Rp '.number_format($saldoBersih, 0, ',', '.')]);

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Export Excel Error: '.$e->getMessage());

            return back()->with('error', 'Gagal generate Excel: '.$e->getMessage());
        }
    }
}
