<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    public function index(Request $request): View
    {
        $dari = $this->tanggal($request->query('dari')) ?? now()->startOfMonth();
        $sampai = $this->tanggal($request->query('sampai')) ?? now()->endOfMonth();
        $kategori = $request->query('kategori');
        $jenis = $request->query('jenis');
        $cari = trim((string) $request->query('q'));

        $transaksis = Transaksi::with('barang')
            ->whereBetween('tanggal', [$dari->copy()->startOfDay(), $sampai->copy()->endOfDay()])
            ->when($cari !== '', fn ($q) => $q->where(function ($q) use ($cari) {
                $q->where('kode_barang', 'like', "%{$cari}%")
                    ->orWhereHas('barang', fn ($b) => $b->where('nama', 'like', "%{$cari}%"))
                    ->orWhere('oleh', 'like', "%{$cari}%")
                    ->orWhere('keterangan', 'like', "%{$cari}%");
            }))
            ->when($kategori, fn ($q) => $q->whereHas('barang', fn ($b) => $b->where('kategori', $kategori)))
            ->when($jenis, fn ($q) => $q->where('jenis', $jenis))
            ->latest('tanggal')
            ->latest('id')
            ->paginate(50)
            ->withQueryString();

        return view('riwayat.index', [
            'transaksis' => $transaksis,
            'dari' => $dari,
            'sampai' => $sampai,
            'kategori' => $kategori,
            'jenis' => $jenis,
            'cari' => $cari,
            'kategoriList' => Barang::daftarKategori(),
        ]);
    }

    /**
     * Catat barang masuk atau keluar; stok langsung diperbarui.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode_barang' => ['required', 'exists:barangs,kode_barang'],
            'jenis' => ['required', 'in:masuk,keluar'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'oleh' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            // Boleh diisi tanggal lampau untuk transaksi yang terlambat dicatat.
            'tanggal' => ['nullable', 'date', 'before_or_equal:now'],
        ], $this->pesan(), $this->atribut());

        DB::transaction(function () use ($data) {
            $barang = Barang::lockForUpdate()->findOrFail($data['kode_barang']);

            if ($data['jenis'] === Transaksi::JENIS_KELUAR && $data['jumlah'] > $barang->stok) {
                throw ValidationException::withMessages([
                    'jumlah' => "Stok {$barang->nama} hanya tersisa {$barang->stok} {$barang->satuan}.",
                ]);
            }

            $barang->transaksis()->create([
                ...$data,
                'tanggal' => $data['tanggal'] ?? now(),
            ]);

            $data['jenis'] === Transaksi::JENIS_MASUK
                ? $barang->increment('stok', $data['jumlah'])
                : $barang->decrement('stok', $data['jumlah']);
        });

        $label = $data['jenis'] === Transaksi::JENIS_MASUK ? 'Barang masuk' : 'Barang keluar';

        return back()->with('success', "{$label} berhasil dicatat.");
    }

    private function tanggal(?string $nilai): ?Carbon
    {
        if (! $nilai) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $nilai);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, string>
     */
    private function pesan(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min.',
            'exists' => ':attribute tidak ditemukan.',
            'in' => ':attribute tidak valid.',
            'date' => ':attribute tidak valid.',
            'before_or_equal' => ':attribute tidak boleh melewati waktu sekarang.',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function atribut(): array
    {
        return [
            'kode_barang' => 'Barang',
            'jenis' => 'Jenis transaksi',
            'jumlah' => 'Jumlah',
            'oleh' => 'Nama pelaku',
            'keterangan' => 'Keterangan',
            'tanggal' => 'Tanggal',
        ];
    }
}
