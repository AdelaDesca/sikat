<?php

namespace App\Providers;

use App\Models\Barang;
use App\Models\Transaksi;
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
        // Data notifikasi (lonceng di topbar): permintaan pending & stok menipis.
        View::composer('components.layouts.app', function ($view) {
            $view->with('notifikasi', [
                'pending' => Transaksi::with('barang')
                    ->where('jenis', Transaksi::JENIS_PERMINTAAN)
                    ->where('status', Transaksi::STATUS_PENDING)
                    ->latest('tanggal')
                    ->take(5)
                    ->get(),
                'menipis' => Barang::whereColumn('stok', '<=', 'stok_minimum')
                    ->orderBy('stok')
                    ->take(5)
                    ->get(),
            ]);
        });
    }
}
