<?php

namespace App\Providers;

use App\Models\BahanBaku;
use App\Models\Produk;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share notifications data with all views
        View::composer('*', function ($view) {
            // Cek role user yang login
            $userRole = auth()->check() ? auth()->user()->role : null;

            // Jika kasir, kosongkan notifikasi tapi tetap kirim variabel
            if ($userRole === 'kasir') {
                $view->with([
                    'notifications' => collect([]),
                    'notificationCount' => 0,
                ]);

                return;
            }

            // Untuk manajemen/manager, tampilkan semua notifikasi
            // Ambil Bahan Baku dengan stok menipis/habis
            $bahanBakuNotifications = BahanBaku::whereColumn('stok', '<=', 'min_stok')
                ->orderBy('stok', 'asc')
                ->get()
                ->map(function ($item) {
                    $percentage = $item->min_stok > 0 ? round(($item->stok / $item->min_stok) * 100) : 0;

                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'stok' => $item->stok,
                        'satuan' => $item->satuan->nama ?? 'pcs',
                        'min_stok' => $item->min_stok,
                        'percentage' => $percentage,
                        'type' => 'bahan_baku',
                        'status' => $item->stok == 0 ? 'Habis' : ($percentage <= 20 ? 'Kritis' : 'Rendah'),
                        'color' => $item->stok == 0 ? 'red' : ($percentage <= 20 ? 'red' : 'yellow'),
                    ];
                });

            // Ambil Produk dengan stok menipis/habis
            $produkNotifications = Produk::whereColumn('stok', '<=', 'min_stok')
                ->orderBy('stok', 'asc')
                ->get()
                ->map(function ($item) {
                    $percentage = $item->min_stok > 0 ? round(($item->stok / $item->min_stok) * 100) : 0;

                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'stok' => $item->stok,
                        'satuan' => 'pcs',
                        'min_stok' => $item->min_stok,
                        'percentage' => $percentage,
                        'type' => 'produk',
                        'status' => $item->stok == 0 ? 'Habis' : ($percentage <= 20 ? 'Kritis' : 'Rendah'),
                        'color' => $item->stok == 0 ? 'red' : ($percentage <= 20 ? 'red' : 'yellow'),
                    ];
                });

            // Gabungkan semua notifikasi
            $notifications = $bahanBakuNotifications->merge($produkNotifications)->sortBy('stok');

            $notificationCount = $notifications->count();

            $view->with([
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
            ]);
        });
    }
}
