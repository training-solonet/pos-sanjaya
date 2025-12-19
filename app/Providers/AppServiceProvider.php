<?php

namespace App\Providers;

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
            // Hanya Produk dengan stok menipis/habis (tanpa bahan baku)
            $notifications = Produk::whereColumn('stok', '<=', 'min_stok')
                ->orderBy('stok', 'asc')
                ->get()
                ->map(function ($item) {
                    $percentage = $item->min_stok > 0 ? round(($item->stok / $item->min_stok) * 100) : 0;

                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'stok' => $item->stok,
                        'min_stok' => $item->min_stok,
                        'percentage' => $percentage,
                        'type' => 'produk',
                        'status' => $item->stok == 0 ? 'Habis' : ($percentage <= 20 ? 'Kritis' : 'Rendah'),
                        'color' => $item->stok == 0 ? 'red' : ($percentage <= 20 ? 'red' : 'yellow'),
                    ];
                });

            // Tidak lagi menggabungkan dengan bahan baku, hanya produk

            $notificationCount = $notifications->count();

            $view->with([
                'notifications' => $notifications,
                'notificationCount' => $notificationCount,
            ]);
        });
    }
}
