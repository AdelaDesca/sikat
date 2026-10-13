<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/lupa-sandi', [AuthController::class, 'lupaSandi'])->name('password.request');
});

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/stok-barang', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/stok-barang', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/stok-barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/stok-barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

    Route::get('/riwayat-barang', [TransaksiController::class, 'index'])->name('riwayat.index');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');

    Route::get('/report', [ReportController::class, 'index'])->name('report.index');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
