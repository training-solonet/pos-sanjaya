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
        // eager-load the related Satuan (big unit) so view can show its name
        // Order by newest first
        $konversi = Konversi::with('satuan')->orderBy('tgl', 'desc')->orderBy('id', 'desc')->get();
        $satuan = Satuan::all();

        return view('manajemen.konversi.index', compact('konversi', 'satuan'));
    }

    public function create()
    {
        $satuan_kecil = ['pcs', 'kg', 'l', 'gram', 'sdt', 'sdm'];

        return view('manajemen.konversi.create', compact('satuan_kecil'));
    }

    public function store(Request $request)
    {
        // Support AJAX/json requests from the UI as well as classic form posts
        if ($request->wantsJson() || $request->ajax()) {
            $name = $request->input('new_satuan_name') ?? $request->input('nama_satuan');
            $code = $request->input('new_satuan_code') ?? $request->input('kode_besar') ?? $request->input('satuan_besar');
            $jumlah = $request->input('jumlah');
            $satuan_kecil = $request->input('satuan_kecil');

            if (! $name || ! $code || ! $jumlah || ! $satuan_kecil) {
                return response()->json(['success' => false, 'message' => 'Missing required fields'], 422);
            }

            // find or create Satuan by name
            $satuan = Satuan::whereRaw('LOWER(nama) = ?', [mb_strtolower($name)])->first();
            if (! $satuan) {
                $satuan = Satuan::create(['nama' => $name]);
            }

            // avoid duplicates for same big/kecil/jumlah
            $existing = Konversi::where('id_satuan', $satuan->id)
                ->where('satuan_kecil', $satuan_kecil)
                ->where('jumlah', (int) $jumlah)
                ->first();

            if ($existing) {
                return response()->json(['success' => true, 'konversi' => $existing]);
            }

            $konv = Konversi::create([
                'id_satuan' => $satuan->id,
                'satuan_besar' => strtoupper($code),
                'jumlah' => (int) $jumlah,
                'satuan_kecil' => $satuan_kecil,
                'nilai' => 1,
                'tgl' => now(), // Always use current timestamp
            ]);

            return response()->json(['success' => true, 'konversi' => $konv, 'satuan' => $satuan]);
        }

        // non-AJAX form submission
        $request->validate([
            'nama_satuan' => 'required|string|max:100',
            'kode_besar' => 'required|string|max:20',
            'jumlah' => 'required|integer|min:1',
            'satuan_kecil' => 'required|string',
        ]);

        $satuan = Satuan::create(['nama' => $request->nama_satuan]);

        Konversi::create([
            'id_satuan' => $satuan->id,
            'satuan_besar' => strtoupper($request->kode_besar),
            'jumlah' => $request->jumlah,
            'satuan_kecil' => $request->satuan_kecil,
            'nilai' => 1,
            'tgl' => now(),
        ]);

        return back()->with('success', 'Konversi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $konversi = Konversi::findOrFail($id);
        $satuan_kecil = ['pcs', 'kg', 'l', 'gram', 'sdt', 'sdm'];

        return view('manajemen.konversi.edit', compact('konversi', 'satuan_kecil'));
    }

    public function update(Request $request, $id)
    {
        // Support AJAX update
        if ($request->wantsJson() || $request->ajax()) {
            $konv = Konversi::findOrFail($id);
            $name = $request->input('new_satuan_name');
            $code = $request->input('new_satuan_code');
            $jumlah = $request->input('jumlah');
            $satuan_kecil = $request->input('satuan_kecil');

            if ($name) {
                $s = $konv->satuan;
                if ($s) {
                    $s->nama = $name;
                    $s->save();
                }
            }

            if ($code) {
                $konv->satuan_besar = strtoupper($code);
            }
            if (! is_null($jumlah)) {
                $konv->jumlah = (int) $jumlah;
            }
            if ($satuan_kecil) {
                $konv->satuan_kecil = $satuan_kecil;
            }
            $konv->tgl = now();
            $konv->save();

            return response()->json(['success' => true, 'konversi' => $konv]);
        }

        // non-AJAX update
        $request->validate([
            'satuan_besar' => 'required|string|max:50',
            'jumlah' => 'required|integer|min:1',
            'satuan_kecil' => 'required|string',
        ]);

        $konv = Konversi::findOrFail($id);
        $konv->update([
            'satuan_besar' => strtoupper($request->satuan_besar),
            'jumlah' => $request->jumlah,
            'satuan_kecil' => $request->satuan_kecil,
            'tgl' => now(),
        ]);

        return back()->with('success', 'Konversi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $deleted = Konversi::destroy($id);
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => (bool) $deleted]);
        }

        return back()->with('success', 'Konversi berhasil dihapus.');
    }
}
