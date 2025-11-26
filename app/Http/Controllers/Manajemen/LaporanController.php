<?php

namespace App\Http\Controllers\manajemen;

use App\Http\Controllers\Controller;

class LaporanController extends Controller
{
    //
    public function index()
    {
        return view('manajemen.laporan.index');
    }
}
