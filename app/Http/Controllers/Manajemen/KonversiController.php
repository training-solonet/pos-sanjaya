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
        $konversi = Konversi::with('satuan')->get();
        $satuan = Satuan::all();
        return view('manajemen.konversi.index', compact('konversi', 'satuan'));
    }

    public function create()
    {
        
        return view('manajemen.konversi.create', compact('satuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_satuan' => 'required|exists:satuan,id',
            'jumlah' => 'required|integer|min:1',
            'satuan_dasar' => 'required|integer|min:1',
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
        Konversi::destroy($id);

        return redirect()->route('management.konversi.index')
            ->with('success', 'Data konversi berhasil dihapus.');
    }
}
