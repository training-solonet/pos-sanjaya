<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use App\Models\Konversi;
use App\Models\Satuan;
use Illuminate\Http\Request;

class KonversiController extends Controller
{
    public function index()
    {
        $konversi = Konversi::with(['satuanBesar', 'satuanKecil'])->get();
        $satuan = Satuan::all();

        return view('manajemen.konversi.index', compact('konversi', 'satuan'));
    }

    public function create()
    {

        return view('manajemen.konversi.create', compact('satuan'));
    }

    public function store(Request $request)
    {
        // Support AJAX creation where client may send `new_satuan_name` to create both
        // a new Satuan and a Konversi entry in one request.
        if ($request->wantsJson() || $request->ajax()) {
            // simple validation for AJAX
            $name = $request->input('new_satuan_name');
            $jumlah = $request->input('jumlah');
            $satuan_kecil = $request->input('satuan_kecil') ?? $request->input('satuan_dasar');

            if (!$name || !$jumlah) {
                return response()->json(['success' => false, 'message' => 'Nama satuan dan jumlah wajib.'], 422);
            }

            // Cari Satuan berdasarkan nama (case-insensitive). Jika sudah ada, gunakan.
            $s = Satuan::whereRaw('LOWER(nama) = ?', [mb_strtolower($name)])->first();
            if (!$s) {
                $s = Satuan::create([ 'nama' => $name ]);
            }

            // Cek apakah Konversi untuk pasangan satuan besar/kecil sudah ada (hindari duplikat)
            $existingKonv = Konversi::where('satuan_besar', $s->id)
                ->where('satuan_kecil', $satuan_kecil)
                ->where('jumlah', (int)$jumlah)
                ->first();

            if ($existingKonv) {
                return response()->json(['success' => true, 'konversi' => $existingKonv, 'satuan' => $s]);
            }

            // create Konversi record
            $konv = Konversi::create([
                'satuan_besar' => $s->id,
                'satuan_kecil' => (int)$satuan_kecil,
                'jumlah' => (int)$jumlah,
                'nilai' => $request->input('nilai') ?? null,
                'tgl' => $request->input('tgl') ?? now()->toDateString(),
            ]);

            return response()->json(['success' => true, 'konversi' => $konv, 'satuan' => $s]);
        }

        // default (non-AJAX) behavior
        $request->validate([
            'satuan_besar' => 'required|exists:satuan,id',
            'jumlah' => 'required|integer|min:1',
            'satuan_kecil' => 'required|exists:satuan,id',
            'tgl' => 'required|date',
        ]);

        Konversi::create($request->all());

        return redirect()->route('management.konversi.index')
            ->with('success', 'Data konversi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $konversi = Konversi::findOrFail($id);
        $satuan = Satuan::all();

        return view('manajemen.konversi.edit', compact('konversi', 'satuan'));
    }

    public function update(Request $request, $id)
    {
        if ($request->wantsJson() || $request->ajax()) {
            $konversi = Konversi::findOrFail($id);
            $konversi->update($request->all());
            return response()->json(['success' => true, 'konversi' => $konversi]);
        }

        $request->validate([
            'id_satuan' => 'required|exists:satuan,id',
            'jumlah' => 'required|integer|min:1',
            'satuan_dasar' => 'required|integer|min:1',
            'tgl' => 'required|date',
        ]);

        $konversi = Konversi::findOrFail($id);
        $konversi->update($request->all());

        return redirect()->route('management.konversi.index')
            ->with('success', 'Data konversi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $deleted = Konversi::destroy($id);
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => (bool)$deleted]);
        }

        return redirect()->route('management.konversi.index')
            ->with('success', 'Data konversi berhasil dihapus.');
    }
}
