<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Excel;

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

            $pdf = Pdf::loadView('kasir.jurnal.export-pdf', $data)
                ->setPaper('a4', 'portrait')
                ->setOption('margin-top', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('margin-left', 10);

            return $pdf->download("Jurnal_Harian_{$tanggal}.pdf");
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export jurnal to Excel for specific date
     */
    public function exportExcel(Request $request)
    {
        try {
            $tanggal = $request->get('tanggal', date('Y-m-d'));
            
            // Get journals for specific date
            $jurnals = Jurnal::whereDate('tgl', $tanggal)
                ->orderBy('tgl', 'asc')
                ->get();

            // Calculate summary
            $totalPemasukan = $jurnals->where('jenis', 'pemasukan')->sum('nominal');
            $totalPengeluaran = $jurnals->where('jenis', 'pengeluaran')->sum('nominal');
            $saldoBersih = $totalPemasukan - $totalPengeluaran;

            // Format tanggal
            $tanggalFormatted = Carbon::parse($tanggal)->format('d-m-Y');
            
            $fileName = "Jurnal_Harian_{$tanggalFormatted}";
            
            // Create Excel using Laravel Excel v1
            Excel::create($fileName, function($excel) use ($jurnals, $totalPemasukan, $totalPengeluaran, $saldoBersih, $tanggalFormatted) {
                
                $excel->sheet('Jurnal Harian', function($sheet) use ($jurnals, $totalPemasukan, $totalPengeluaran, $saldoBersih, $tanggalFormatted) {
                    
                    // Set title
                    $sheet->mergeCells('A1:F1');
                    $sheet->row(1, ['JURNAL HARIAN']);
                    $sheet->row(1, function($row) {
                        $row->setFontSize(16);
                        $row->setFontWeight('bold');
                        $row->setAlignment('center');
                    });
                    
                    // Set date
                    $sheet->mergeCells('A2:F2');
                    $sheet->row(2, ['Tanggal: ' . $tanggalFormatted]);
                    $sheet->row(2, function($row) {
                        $row->setAlignment('center');
                    });
                    
                    // Add empty row
                    $sheet->row(3, ['']);
                    
                    // Set header
                    $sheet->row(4, ['No', 'Tanggal', 'Jenis', 'Kategori', 'Keterangan', 'Nominal']);
                    $sheet->row(4, function($row) {
                        $row->setFontWeight('bold');
                        $row->setBackground('#4F46E5');
                        $row->setFontColor('#FFFFFF');
                        $row->setAlignment('center');
                    });
                    
                    // Add data
                    $rowNumber = 5;
                    $no = 1;
                    foreach ($jurnals as $jurnal) {
                        $sheet->row($rowNumber, [
                            $no++,
                            Carbon::parse($jurnal->tgl)->format('d/m/Y'),
                            ucfirst($jurnal->jenis),
                            $jurnal->kategori,
                            $jurnal->keterangan,
                            'Rp ' . number_format($jurnal->nominal, 0, ',', '.')
                        ]);
                        $rowNumber++;
                    }
                    
                    // Add summary
                    $rowNumber += 1;
                    $sheet->row($rowNumber, ['', '', '', '', 'Total Pemasukan:', 'Rp ' . number_format($totalPemasukan, 0, ',', '.')]);
                    $sheet->row($rowNumber, function($row) {
                        $row->setFontWeight('bold');
                    });
                    
                    $rowNumber++;
                    $sheet->row($rowNumber, ['', '', '', '', 'Total Pengeluaran:', 'Rp ' . number_format($totalPengeluaran, 0, ',', '.')]);
                    $sheet->row($rowNumber, function($row) {
                        $row->setFontWeight('bold');
                    });
                    
                    $rowNumber++;
                    $sheet->row($rowNumber, ['', '', '', '', 'Saldo Bersih:', 'Rp ' . number_format($saldoBersih, 0, ',', '.')]);
                    $sheet->row($rowNumber, function($row) {
                        $row->setFontWeight('bold');
                        $row->setBackground('#E0E7FF');
                    });
                    
                    // Auto size columns
                    $sheet->setAutoSize(true);
                });
                
            })->download('xlsx');
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal generate Excel: ' . $e->getMessage()
            ], 500);
        }
    }
}
