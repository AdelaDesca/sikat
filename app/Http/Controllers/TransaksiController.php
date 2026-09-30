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

        $transaksis = Transaksi::with('barang')
            ->whereBetween('tanggal', [$dari->copy()->startOfDay(), $sampai->copy()->endOfDay()])
            ->when($kategori, fn ($q) => $q->whereHas('barang', fn ($b) => $b->where('kategori', $kategori)))
            ->when($jenis, fn ($q) => $q->where('jenis', $jenis))
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('riwayat.index', [
            'transaksis' => $transaksis,
            'dari' => $dari,
            'sampai' => $sampai,
            'kategori' => $kategori,
            'jenis' => $jenis,
            'kategoriList' => Barang::daftarKategori(),
            'semuaBarang' => Barang::orderBy('nama')->get(['id', 'nama', 'stok', 'satuan']),
        ]);
    }

    /**
     * Catat barang masuk atau keluar; stok langsung diperbarui.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'jenis' => ['required', 'in:masuk,keluar'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'oleh' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], $this->pesan(), $this->atribut());

        DB::transaction(function () use ($data, $request) {
            $barang = Barang::lockForUpdate()->findOrFail($data['barang_id']);

            if ($data['jenis'] === Transaksi::JENIS_KELUAR && $data['jumlah'] > $barang->stok) {
                throw ValidationException::withMessages([
                    'jumlah' => "Stok {$barang->nama} hanya tersisa {$barang->stok} {$barang->satuan}.",
                ]);
            }

            $barang->transaksis()->create([
                ...$data,
                'user_id' => $request->user()->id,
                'status' => Transaksi::STATUS_SELESAI,
                'tanggal' => now(),
            ]);

            $data['jenis'] === Transaksi::JENIS_MASUK
                ? $barang->increment('stok', $data['jumlah'])
                : $barang->decrement('stok', $data['jumlah']);
        });

        $label = $data['jenis'] === Transaksi::JENIS_MASUK ? 'Barang masuk' : 'Barang keluar';

        return back()->with('success', "{$label} berhasil dicatat.");
    }

    /**
     * Ajukan permintaan restock ATK; stok baru bertambah setelah disetujui.
     */
    public function storePermintaan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], $this->pesan(), $this->atribut());

        Transaksi::create([
            ...$data,
            'user_id' => $request->user()->id,
            'jenis' => Transaksi::JENIS_PERMINTAAN,
            'oleh' => $request->user()->name,
            'status' => Transaksi::STATUS_PENDING,
            'tanggal' => now(),
        ]);

        return back()->with('success', 'Permintaan ATK berhasil diajukan dan menunggu persetujuan.');
    }

    public function setujui(Transaksi $transaksi): RedirectResponse
    {
        $this->pastikanPending($transaksi);

        DB::transaction(function () use ($transaksi) {
            $transaksi->update(['status' => Transaksi::STATUS_SELESAI]);
            $transaksi->barang()->increment('stok', $transaksi->jumlah);
        });

        return back()->with('success', 'Permintaan disetujui, stok telah ditambahkan.');
    }

    public function tolak(Transaksi $transaksi): RedirectResponse
    {
        $this->pastikanPending($transaksi);

        $transaksi->update(['status' => Transaksi::STATUS_DITOLAK]);

        return back()->with('success', 'Permintaan ditolak.');
    }

    private function pastikanPending(Transaksi $transaksi): void
    {
        abort_unless(
            $transaksi->jenis === Transaksi::JENIS_PERMINTAAN && $transaksi->status === Transaksi::STATUS_PENDING,
            422,
            'Permintaan ini sudah diproses.'
        );
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
        ];
    }

    /**
     * @return array<string, string>
     */
    private function atribut(): array
    {
        return [
            'barang_id' => 'Barang',
            'jenis' => 'Jenis transaksi',
            'jumlah' => 'Jumlah',
            'oleh' => 'Nama pelaku',
            'keterangan' => 'Keterangan',
        ];
    }
}
