<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Routes for role 'manajemen' (controllers in App\Http\Controllers\Manajemen)
    Route::middleware('role:manajemen')
        ->prefix('manajemen')
        ->name('manajemen.')
        ->group(function () {
            // Dashboard (only index)
            Route::get('/', [\App\Http\Controllers\Manajemen\DashboardController::class, 'index'])->name('dashboard');

            // Resource routes for management controllers
            Route::resource('bahanbaku', \App\Http\Controllers\Manajemen\BahanbakuController::class);
            Route::resource('produk', \App\Http\Controllers\Manajemen\ProdukController::class);
            Route::resource('resep', \App\Http\Controllers\Manajemen\ResepController::class);
            Route::resource('konversi', \App\Http\Controllers\Manajemen\KonversiController::class);
            Route::resource('jurnal', \App\Http\Controllers\Manajemen\JurnalController::class);
        });

    // Routes for role 'kasir' (controllers in App\Http\Controllers\Kasir)
    Route::middleware('role:kasir')
        ->prefix('kasir')
        ->name('kasir.')
        ->group(function () {
            // Dashboard (only index)
            Route::get('/', [\App\Http\Controllers\Kasir\DashboardController::class, 'index'])->name('dashboard');

            // Resource routes for kasir controllers
            Route::resource('transaksi', \App\Http\Controllers\Kasir\TransaksiController::class);
            Route::resource('jurnal', \App\Http\Controllers\Kasir\JurnalController::class);
            Route::resource('laporan', \App\Http\Controllers\Kasir\LaporanController::class);
        });
});


route::get("/reg", function(  ){
    return view("auth.register");
});
