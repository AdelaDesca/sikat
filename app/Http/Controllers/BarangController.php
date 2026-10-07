<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function index(Request $request): View
    {
        $cari = trim((string) $request->query('q'));

        $barangs = Barang::query()
            ->when($cari !== '', fn ($q) => $q->where(function ($q) use ($cari) {
                $q->where('kode_barang', 'like', "%{$cari}%")
                    ->orWhere('nama', 'like', "%{$cari}%")
                    ->orWhere('kategori', 'like', "%{$cari}%");
            }))
            ->orderBy('kode_barang')
            ->paginate(50)
            ->withQueryString();

        return view('barang.index', [
            'barangs' => $barangs,
            'cari' => $cari,
            'semuaBarang' => Barang::orderBy('nama')->get(['kode_barang', 'nama', 'stok', 'satuan']),
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
                    'jenis' => Transaksi::JENIS_MASUK,
                    'jumlah' => $barang->stok,
                    'oleh' => $request->user()->name,
                    'keterangan' => 'Stok awal barang baru',
                    'tanggal' => now(),
                ]);
            }
        });

        return redirect()->route('barang.index')->with('success', 'ATK baru berhasil ditambahkan.');
    }

    public function update(Request $request, Barang $barang): RedirectResponse
    {
        $data = $this->validasi($request, $barang);

        DB::transaction(function () use ($data, $barang, $request) {
            $stokLama = $barang->stok;
            $barang->update($data);

            // Koreksi salah input stok dicatat terpisah, bukan sebagai barang masuk/keluar.
            $selisih = $barang->stok - $stokLama;
            if ($selisih !== 0) {
                $barang->transaksis()->create([
                    'jenis' => Transaksi::JENIS_KOREKSI,
                    'jumlah' => $selisih,
                    'oleh' => $request->user()->name,
                    'keterangan' => "Koreksi stok {$stokLama} → {$barang->stok}",
                    'tanggal' => now(),
                ]);
            }
        });

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
    private function validasi(Request $request, ?Barang $barang = null): array
    {
        $request->merge(['kode_barang' => strtoupper(trim((string) $request->input('kode_barang')))]);

        $aturan = [
            'kode_barang' => [
                'required', 'max:10', 'regex:/^[A-Z0-9-]+$/',
                Rule::unique('barangs', 'kode_barang')->ignore($barang?->kode_barang, 'kode_barang'),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'satuan' => ['required', 'string', 'max:30'],
            'stok' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
        ];

        return $request->validate($aturan, [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max karakter.',
            'regex' => ':attribute hanya boleh berisi huruf, angka, dan tanda -.',
            'unique' => ':attribute sudah dipakai barang lain.',
        ], [
            'kode_barang' => 'Kode barang',
            'nama' => 'Nama barang',
            'kategori' => 'Kategori',
            'satuan' => 'Satuan',
            'stok' => 'Stok',
            'stok_minimum' => 'Batas hampir habis',
        ]);
    }
}
