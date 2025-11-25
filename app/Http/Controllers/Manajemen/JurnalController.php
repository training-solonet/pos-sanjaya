<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jurnal;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    public function index()
    {
        // Ambil data jurnal untuk hari ini
        $today = now()->format('Y-m-d');
        $jurnals = Jurnal::whereDate('tgl', $today)
                        ->where('role', 'manajemen')
                        ->orderBy('tgl', 'desc')
                        ->get();

        return view("manajemen.jurnal.index", compact('jurnals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl' => 'required|date',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'keterangan' => 'required|string|max:500',
            'nominal' => 'required|integer|min:1',
            'kategori' => 'required|in:Operasional,Utilitas,Bahan Baku,Penjualan,Transportasi,lainnya'
        ]);

        try {
            Jurnal::create([
                'tgl' => $validated['tgl'],
                'jenis' => $validated['jenis'],
                'keterangan' => $validated['keterangan'],
                'nominal' => $validated['nominal'],
                'kategori' => $validated['kategori'],
                'role' => 'manajemen'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil ditambahkan!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tgl' => 'required|date',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'keterangan' => 'required|string|max:500',
            'nominal' => 'required|integer|min:1',
            'kategori' => 'required|in:Operasional,Utilitas,Bahan Baku,Penjualan,Transportasi,lainnya'
        ]);

        try {
            $jurnal = Jurnal::where('id', $id)
                          ->where('role', 'manajemen')
                          ->firstOrFail();

            $jurnal->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diupdate!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $jurnal = Jurnal::where('id', $id)
                          ->where('role', 'manajemen')
                          ->firstOrFail();

            $jurnal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    // API untuk mendapatkan data jurnal berdasarkan filter
    public function getData(Request $request)
    {
        $query = Jurnal::where('role', 'manajemen');

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('tgl', $request->date);
        }

        // Filter by jenis
        if ($request->has('jenis') && $request->jenis != 'semua') {
            $query->where('jenis', $request->jenis);
        }

        // Filter by kategori
        if ($request->has('kategori') && $request->kategori != 'semua') {
            $query->where('kategori', $request->kategori);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('keterangan', 'like', '%' . $request->search . '%');
        }

        $jurnals = $query->orderBy('tgl', 'desc')->get();

        return response()->json($jurnals);
    }

    // API untuk mendapatkan summary
    public function getSummary(Request $request)
    {
        $query = Jurnal::where('role', 'manajemen');

        if ($request->has('date') && $request->date) {
            $query->whereDate('tgl', $request->date);
        }

        $data = $query->select(
            DB::raw('COUNT(*) as total_transaksi'),
            DB::raw('SUM(CASE WHEN jenis = "pemasukan" THEN nominal ELSE 0 END) as total_pemasukan'),
            DB::raw('SUM(CASE WHEN jenis = "pengeluaran" THEN nominal ELSE 0 END) as total_pengeluaran'),
            DB::raw('COUNT(CASE WHEN jenis = "pemasukan" THEN 1 END) as jumlah_pemasukan'),
            DB::raw('COUNT(CASE WHEN jenis = "pengeluaran" THEN 1 END) as jumlah_pengeluaran')
        )->first();

        return response()->json([
            'total_revenue' => $data->total_pemasukan ?? 0,
            'total_expense' => $data->total_pengeluaran ?? 0,
            'net_balance' => ($data->total_pemasukan ?? 0) - ($data->total_pengeluaran ?? 0),
            'revenue_count' => $data->jumlah_pemasukan ?? 0,
            'expense_count' => $data->jumlah_pengeluaran ?? 0
        ]);
    }
}