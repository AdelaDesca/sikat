@php
    $jenisBadge = [
        'keluar' => ['Keluar', 'bg-amber-100 text-amber-600'],
        'masuk' => ['Masuk', 'bg-blue-100 text-blue-600'],
        'koreksi' => ['Koreksi', 'bg-slate-100 text-slate-600'],
    ];
    $adaFilter = request()->hasAny(['dari', 'sampai', 'kategori', 'jenis', 'q']);
@endphp

<x-layouts.app title="Riwayat Barang">
    <form method="GET" action="{{ route('riwayat.index') }}" data-live-search="hasil-riwayat" class="flex flex-wrap items-center gap-3 sm:gap-5">
        <div class="relative w-full sm:w-72">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-[18px] -translate-y-1/2 text-slate-500" />
            <input type="search" name="q" value="{{ $cari }}" placeholder="Cari barang, kode, nama peminta..."
                class="form-input py-2.5 pl-11">
        </div>

        <div class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-[15px]">
            <x-icon name="calendar" class="size-[18px] shrink-0 text-slate-600" />
            <input type="date" name="dari" value="{{ $dari->format('Y-m-d') }}" onchange="this.form.submit()"
                class="w-[8.5rem] bg-transparent focus:outline-none" aria-label="Dari tanggal">
            <span class="text-slate-400">–</span>
            <input type="date" name="sampai" value="{{ $sampai->format('Y-m-d') }}" onchange="this.form.submit()"
                class="w-[8.5rem] bg-transparent focus:outline-none" aria-label="Sampai tanggal">
        </div>

        <select name="kategori" onchange="this.form.submit()"
            class="rounded-lg border border-slate-200 bg-white py-2.5 pr-9 pl-4 text-[15px] focus:outline-none">
            <option value="">Semua Kategori</option>
            @foreach ($kategoriList as $k)
                <option value="{{ $k }}" @selected($kategori === $k)>{{ $k }}</option>
            @endforeach
        </select>

        <select name="jenis" onchange="this.form.submit()"
            class="rounded-lg border border-slate-200 bg-white py-2.5 pr-9 pl-4 text-[15px] focus:outline-none">
            <option value="">Semua Transaksi</option>
            @foreach ($jenisBadge as $val => [$label])
                <option value="{{ $val }}" @selected($jenis === $val)>{{ $label }}</option>
            @endforeach
        </select>

        @if ($adaFilter)
            <a href="{{ route('riwayat.index') }}" class="text-[15px] font-semibold text-brand-500 hover:text-brand-600">Reset Filter</a>
        @endif
        <noscript><button type="submit" class="btn btn-outline">Terapkan</button></noscript>
    </form>

    <div id="hasil-riwayat">
    <div class="card mt-6 overflow-x-auto">
        <table class="w-full">
            <thead class="table-head">
                <tr>
                    <th>Tanggal</th>
                    <th>Nama ATK</th>
                    <th>Jenis Transaksi</th>
                    <th>Jumlah</th>
                    <th>Oleh</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse ($transaksis as $t)
                    @php [$label, $kelas] = $jenisBadge[$t->jenis]; @endphp
                    <tr>
                        <td class="text-slate-500">{{ $t->tanggal->translatedFormat('d M Y H:i') }}</td>
                        <td class="font-semibold">{{ $t->barang->nama }}</td>
                        <td><span class="badge {{ $kelas }}">{{ $label }}</span></td>
                        <td @class(['font-bold', 'text-emerald-600' => $t->isPenambahan()])>
                            {{ $t->isPenambahan() ? '+' : '-' }}{{ abs($t->jumlah) }} {{ $t->barang->satuan }}
                        </td>
                        <td class="text-slate-700">{{ $t->oleh }}</td>
                        <td class="text-slate-500">{{ $t->keterangan ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-slate-500">
                            {{ $cari ? "Tidak ada transaksi yang cocok dengan \"{$cari}\" pada periode ini." : 'Tidak ada transaksi pada periode ini.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $transaksis->links() }}</div>
    </div>
</x-layouts.app>
