<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /** Urutan status dari kondisi terburuk, dipakai konsisten di semua grafik & tabel. */
    private const STATUS = [Barang::STATUS_HABIS, Barang::STATUS_HAMPIR_HABIS, Barang::STATUS_TERSEDIA];

    public function index(Request $request): View
    {
        $kategori = $request->query('kategori') ?: null;

        // Status dihitung di database dengan aturan yang sama seperti Barang::status.
        $statusSql = "CASE WHEN stok <= 0 THEN '".Barang::STATUS_HABIS."'
            WHEN stok <= stok_minimum THEN '".Barang::STATUS_HAMPIR_HABIS."'
            ELSE '".Barang::STATUS_TERSEDIA."' END";

        $barang = Barang::query()->when($kategori, fn ($q) => $q->where('kategori', $kategori));

        $perKategori = (clone $barang)
            ->select('kategori', DB::raw("{$statusSql} as status"), DB::raw('COUNT(*) as jumlah'))
            ->groupBy('kategori', 'status')
            ->toBase() // baris mentah, agar kolom "status" tidak tertimpa accessor Barang::status
            ->get()
            ->groupBy('kategori')
            ->map(fn ($baris) => collect(self::STATUS)->mapWithKeys(
                fn ($s) => [$s => (int) ($baris->firstWhere('status', $s)?->jumlah ?? 0)]
            ))
            // Kategori dengan barang habis terbanyak di atas.
            ->sortByDesc(fn ($s) => [$s[Barang::STATUS_HABIS], $s[Barang::STATUS_HAMPIR_HABIS]]);

        $ringkasan = collect(self::STATUS)->mapWithKeys(
            fn ($s) => [$s => $perKategori->sum(fn ($k) => $k[$s])]
        );

        $kritis = (clone $barang)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok')
            ->orderByRaw('stok_minimum DESC')
            ->orderBy('kode_barang')
            ->get(['kode_barang', 'nama', 'kategori', 'stok', 'satuan', 'stok_minimum']);

        return view('report.index', [
            'kategori' => $kategori,
            'kategoriList' => Barang::daftarKategori(),
            'total' => $ringkasan->sum(),
            'ringkasan' => $ringkasan,
            'perKategori' => $perKategori,
            'kritis' => $kritis,
        ]);
    }
}
