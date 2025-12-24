<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\custommer;
use Illuminate\Http\Request;

class CustommerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('kasir.custommer.index');
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
    public function show(custommer $custommer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(custommer $custommer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, custommer $custommer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(custommer $custommer)
    {
        //
    }
}
