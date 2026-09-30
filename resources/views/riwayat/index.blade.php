@php
    $jenisBadge = [
        'keluar' => ['Keluar', 'bg-amber-100 text-amber-600'],
        'masuk' => ['Masuk', 'bg-blue-100 text-blue-600'],
        'permintaan' => ['Permintaan', 'bg-orange-100 text-orange-500'],
    ];
    $adaFilter = request()->hasAny(['dari', 'sampai', 'kategori', 'jenis']);
@endphp

<x-layouts.app title="Riwayat Keluar Masuk ATK">
    <form method="GET" action="{{ route('riwayat.index') }}" class="flex flex-wrap items-center gap-3 sm:gap-5">
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
                    <th></th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse ($transaksis as $t)
                    @php
                        [$label, $kelas] = $jenisBadge[$t->jenis];
                        $ditolak = $t->status === \App\Models\Transaksi::STATUS_DITOLAK;
                        $pending = $t->status === \App\Models\Transaksi::STATUS_PENDING;
                    @endphp
                    <tr @class(['opacity-60' => $ditolak])>
                        <td class="text-slate-500">{{ $t->tanggal->translatedFormat('d M H:i') }}</td>
                        <td class="font-semibold">{{ $t->barang->nama }}</td>
                        <td>
                            <span class="badge {{ $kelas }}">{{ $label }}</span>
                            @if ($pending)
                                <span class="ml-1 text-xs font-medium text-slate-500">Menunggu</span>
                            @elseif ($ditolak)
                                <span class="ml-1 text-xs font-medium text-red-500">Ditolak</span>
                            @endif
                        </td>
                        <td @class([
                            'font-bold',
                            'text-emerald-600' => $t->isPenambahan(),
                            'line-through' => $ditolak,
                        ])>
                            {{ $t->isPenambahan() ? '+' : '-' }}{{ $t->jumlah }} {{ $t->barang->satuan }}
                        </td>
                        <td class="text-slate-700">{{ $t->oleh }}</td>
                        <td class="text-slate-500">{{ $t->keterangan ?: '—' }}</td>
                        <td class="text-right">
                            @if ($pending)
                                <div class="flex justify-end gap-1">
                                    <form method="POST" action="{{ route('permintaan.setujui', $t) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Setujui"
                                            class="cursor-pointer rounded-lg p-2 text-emerald-600 hover:bg-emerald-50">
                                            <x-icon name="check" class="size-4" />
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('permintaan.tolak', $t) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Tolak"
                                            class="cursor-pointer rounded-lg p-2 text-red-600 hover:bg-red-50">
                                            <x-icon name="x" class="size-4" />
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-500">Tidak ada transaksi pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $transaksis->links() }}</div>
</x-layouts.app>
