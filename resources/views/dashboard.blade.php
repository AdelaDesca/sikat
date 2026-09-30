@php
    $kartu = [
        ['label' => 'Total Barang', 'nilai' => $statistik['total_barang'], 'ket' => 'Jenis ATK terdaftar', 'icon' => 'archive'],
        ['label' => 'Stok Tersedia', 'nilai' => $statistik['stok_tersedia'], 'ket' => 'Unit siap distribusi', 'icon' => 'shield'],
        ['label' => 'Permintaan Pending', 'nilai' => $statistik['permintaan_pending'], 'ket' => 'Menunggu persetujuan', 'icon' => 'hourglass'],
        ['label' => 'Barang Keluar Bulan Ini', 'nilai' => $statistik['keluar_bulan_ini'], 'ket' => 'Didistribusikan', 'icon' => 'external'],
    ];
@endphp

<x-layouts.app title="Ringkasan Logistik">
    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kartu as $k)
            <div class="card p-6">
                <div class="flex items-start justify-between gap-3">
                    <p class="pt-2 text-[15px] font-medium text-slate-500">{{ $k['label'] }}</p>
                    <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-slate-50 text-navy-800">
                        <x-icon :name="$k['icon']" class="size-[18px]" />
                    </span>
                </div>
                <p class="mt-4 text-4xl font-extrabold tracking-tight">{{ number_format($k['nilai']) }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $k['ket'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-10 flex items-center justify-between">
        <h2 class="text-lg font-bold">Aktivitas Pengeluaran Stok Terbaru</h2>
        <a href="{{ route('riwayat.index', ['jenis' => 'keluar']) }}" class="text-sm font-semibold text-brand-500 hover:text-brand-600">
            Lihat semua
        </a>
    </div>

    <div class="card mt-4 overflow-x-auto">
        <table class="w-full">
            <thead class="table-head">
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse ($aktivitas as $t)
                    <tr>
                        <td class="text-slate-600">{{ $t->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="font-semibold">{{ $t->barang->nama }}</td>
                        <td class="text-slate-500">{{ $t->barang->kategori }}</td>
                        <td>{{ $t->jumlah }} {{ $t->barang->satuan }}</td>
                        <td><x-status-badge :status="$t->barang->status" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-500">Belum ada aktivitas pengeluaran stok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
