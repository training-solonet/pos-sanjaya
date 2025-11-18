<?php

use Illuminate\Support\Facades\Route;
use http\controller\App\Http\Controllers\Manajemen;
use App\Http\Controllers\Manajemen\ProdukController;

Route::get('/', function () {
    return view('index');
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


Route::resource('/manajemen/produk', ProdukController::class);

Route::get("/manajemen-produk", function(){
    return view("manajemen.produk.index");
})->name("manajemen_produk");

Route::get("/manajemen-resep", function(){
    return view("manajemen.resep.index");
})->name("manajemen_resep");

Route::get("/manajemen-jurnal", function(){
    return view("manajemen.jurnal.index");
})->name("manajemen_jurnal");

Route::get("/manajemen-konversi", function(){
    return view("manajemen.konversi.index");
})->name("manajemen_konversi");

Route::get("/manajemen-bahanbaku", function(){
    return view("manajemen.bahanbaku.index");
})->name("manajemen_bahanbaku");

Route::get("/manajemen-laporan", function(){
    return view("manajemen.laporan.index");
})->name("manajemen_laporan");

route::get('/manajemen-admin', function(){
    return view("layouts.manajemen.index");
})->name("manajemen");
