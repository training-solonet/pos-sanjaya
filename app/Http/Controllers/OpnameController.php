<?php

namespace App\Http\Controllers;

use App\Models\Opname;
use Illuminate\Http\Request;

class OpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("manajemen.bahanbaku.opname");
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
    public function show(Opname $opname)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Opname $opname)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Opname $opname)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Opname $opname)
    {
        //
    }
}
