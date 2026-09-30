<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $awalBulan = now()->startOfMonth();

        $statistik = [
            'total_barang' => Barang::count(),
            'stok_tersedia' => (int) Barang::sum('stok'),
            'permintaan_pending' => Transaksi::where('jenis', Transaksi::JENIS_PERMINTAAN)
                ->where('status', Transaksi::STATUS_PENDING)
                ->count(),
            'keluar_bulan_ini' => (int) Transaksi::where('jenis', Transaksi::JENIS_KELUAR)
                ->where('tanggal', '>=', $awalBulan)
                ->sum('jumlah'),
        ];

        $aktivitas = Transaksi::with('barang')
            ->where('jenis', Transaksi::JENIS_KELUAR)
            ->latest('tanggal')
            ->take(6)
            ->get();

        return view('dashboard', compact('statistik', 'aktivitas'));
    }
}
