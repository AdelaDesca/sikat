<?php

namespace App\Providers;

use App\Models\Barang;
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
        // Data notifikasi (lonceng di topbar): stok habis & hampir habis.
        View::composer('components.layouts.app', function ($view) {
            $view->with('stokMenipis', Barang::whereColumn('stok', '<=', 'stok_minimum')
                ->orderBy('stok')
                ->orderBy('kode_barang')
                ->get(['kode_barang', 'nama', 'stok', 'satuan']));
        });
    }
}
