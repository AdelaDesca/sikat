<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('q'));

        $barangs = Barang::query()
            ->when($cari !== '', fn ($q) => $q->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('kategori', 'like', "%{$cari}%");
            }))
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('barang.index', [
            'barangs' => $barangs,
            'cari' => $cari,
            'semuaBarang' => Barang::orderBy('nama')->get(['id', 'nama', 'stok', 'satuan']),
            'kategoriList' => Barang::daftarKategori(),
            'satuanList' => Barang::SATUAN,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);

        DB::transaction(function () use ($data, $request) {
            $barang = Barang::create($data);

            // Stok awal dicatat sebagai barang masuk agar riwayat tetap lengkap.
            if ($barang->stok > 0) {
                $barang->transaksis()->create([
                    'user_id' => $request->user()->id,
                    'jenis' => Transaksi::JENIS_MASUK,
                    'jumlah' => $barang->stok,
                    'oleh' => $request->user()->name,
                    'keterangan' => 'Stok awal barang baru',
                    'status' => Transaksi::STATUS_SELESAI,
                    'tanggal' => now(),
                ]);
            }
        });

        return redirect()->route('barang.index')->with('success', 'ATK baru berhasil ditambahkan.');
    }

    public function update(Request $request, Barang $barang): RedirectResponse
    {
        $data = $this->validasi($request, ubah: true);

        $barang->update($data);

        return back()->with('success', "Data {$barang->nama} berhasil diperbarui.");
    }

    public function destroy(Barang $barang): RedirectResponse
    {
        $nama = $barang->nama;
        $barang->delete();

        return back()->with('success', "{$nama} berhasil dihapus.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validasi(Request $request, bool $ubah = false): array
    {
        $aturan = [
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'satuan' => ['required', 'string', 'max:30'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
        ];

        // Stok hanya diisi saat membuat barang; perubahan berikutnya lewat transaksi.
        if (! $ubah) {
            $aturan['stok'] = ['required', 'integer', 'min:0'];
        }

        return $request->validate($aturan, [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min.',
        ], [
            'nama' => 'Nama barang',
            'kategori' => 'Kategori',
            'satuan' => 'Satuan',
            'stok' => 'Stok awal',
            'stok_minimum' => 'Batas hampir habis',
        ]);
    }
}
