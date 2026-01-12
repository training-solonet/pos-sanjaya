<?php

use App\Http\Controllers\Kasir\CustomerController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\JurnalController as KasirJurnalController;
use App\Http\Controllers\Kasir\LaporanController as KasirLaporanController;
use App\Http\Controllers\Kasir\ShiftController;
use App\Http\Controllers\Kasir\TransaksiController;
use App\Http\Controllers\Manajemen\BahanbakuController;
use App\Http\Controllers\Manajemen\DashboardController;
use App\Http\Controllers\Manajemen\JurnalController;
use App\Http\Controllers\Manajemen\KonversiController;
use App\Http\Controllers\Manajemen\LaporanController;
use App\Http\Controllers\Manajemen\OpnameController;
use App\Http\Controllers\Manajemen\ProdukController;
use App\Http\Controllers\Manajemen\ResepController;
use App\Http\Controllers\Manajemen\SettingController;
use App\Http\Controllers\Manajemen\ShiftManController;
use App\Http\Controllers\Manajemen\UpdateStokProdukController;
use App\Http\Controllers\RedirectController;
use App\Http\Middleware\IsKasir;
use App\Http\Middleware\IsManagement;
use Illuminate\Support\Facades\Route;

Route::get('/redirect', [RedirectController::class, 'redirectToRoleBasedDashboard']);

Route::get('/', function () {
    return redirect('/redirect');
});

// Logout route
Route::post('/logout', [RedirectController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware([IsKasir::class])->group(function () {
    // Route group kasir
    Route::group(['prefix' => 'kasir', 'as' => 'kasir.'], function () {
        // Resource routes
        Route::resources([
            'dashboard' => KasirDashboardController::class,
            'jurnal' => KasirJurnalController::class,
            'transaksi' => TransaksiController::class,
            'laporan' => KasirLaporanController::class,
            'shift' => ShiftController::class,
            'customer' => CustomerController::class,
        ]);
    });
});

Route::middleware([IsManagement::class])->group(function () {
    // Route group management
    Route::group(['prefix' => 'management', 'as' => 'management.'], function () {
        Route::resources([
            'dashboard' => DashboardController::class,
            'bahanbaku' => BahanbakuController::class,
            'produk' => ProdukController::class,
            'resep' => ResepController::class,
            'konversi' => KonversiController::class,
            'jurnal' => JurnalController::class,
            'laporan' => LaporanController::class,
            'updateproduk' => UpdateStokProdukController::class,
            'opname' => OpnameController::class,
            'setting' => SettingController::class,
            'shiftman' => ShiftManController::class,
        ]);
    });
});
