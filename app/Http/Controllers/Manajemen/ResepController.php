<?php

namespace App\Http\Controllers\Manajemen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Load resep with rincian bahan to pass to the view
        $resep = \App\Models\Resep::with('rincianResep.bahanBaku')->get();

        $recipes = $resep->map(function ($r) {
            $ingredients = $r->rincianResep->map(function ($ir) {
                return [
                    'name' => optional($ir->bahanBaku)->nama ?? '',
                    'quantity' => $ir->qty ?? 0,
                    'unit' => $ir->hitungan ?? '',
                    'price' => $ir->harga ?? 0,
                    'subtotal' => $ir->harga ?? 0,
                ];
            })->toArray();

            $foodCost = array_sum(array_column($ingredients, 'subtotal'));

            return [
                'id' => $r->id,
                'name' => $r->nama,
                'category' => $r->kategori ?? '',
                'yield' => $r->porsi ?? 1,
                'duration' => $r->waktu_pembuatan ?? '',
                'foodCost' => $foodCost,
                'sellingPrice' => $r->harga_jual ?? null,
                'margin' => 0,
                'status' => $r->status ?? 'Draft',
                'ingredients' => $ingredients,
                'instructions' => $r->langkah ?? '',
                'notes' => $r->catatan ?? '',
            ];
        })->toArray();

        return view('manajemen.resep.index', compact('resep', 'recipes'));
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
