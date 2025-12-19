<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\BahanBaku;
use App\Models\Produk;

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
            // Bahan Baku dengan stok menipis/habis
            $lowStockBahanBaku = BahanBaku::whereColumn('stok', '<=', 'min_stok')
                ->orderBy('stok', 'asc')
                ->get()
                ->map(function ($item) {
                    $percentage = $item->min_stok > 0 ? round(($item->stok / $item->min_stok) * 100) : 0;
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'stok' => $item->stok,
                        'satuan' => $item->satuan,
                        'min_stok' => $item->min_stok,
                        'percentage' => $percentage,
                        'type' => 'bahan_baku',
                        'status' => $item->stok == 0 ? 'Habis' : ($percentage <= 20 ? 'Kritis' : 'Rendah'),
                        'color' => $item->stok == 0 ? 'red' : ($percentage <= 20 ? 'red' : 'yellow')
                    ];
                });

            // Produk dengan stok menipis/habis
            $lowStockProduk = Produk::whereColumn('stok', '<=', 'min_stok')
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
                        'color' => $item->stok == 0 ? 'red' : ($percentage <= 20 ? 'red' : 'yellow')
                    ];
                });

            // Gabungkan dan urutkan berdasarkan tingkat kritis
            $notifications = $lowStockBahanBaku->concat($lowStockProduk)
                ->sortBy('stok')
                ->values();

            $notificationCount = $notifications->count();

            $view->with([
                'notifications' => $notifications,
                'notificationCount' => $notificationCount
            ]);
        });
    }
}
