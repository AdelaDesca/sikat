@php
    use App\Models\Barang;

    // Warna status tetap (hijau/kuning/merah), selalu ditemani ikon + label agar tidak bergantung pada warna saja.
    $status = [
        Barang::STATUS_HABIS => ['label' => 'Habis', 'warna' => '#d03b3b', 'icon' => 'status-habis', 'teks' => 'text-red-600'],
        Barang::STATUS_HAMPIR_HABIS => ['label' => 'Hampir Habis', 'warna' => '#fab219', 'icon' => 'alert', 'teks' => 'text-amber-600'],
        Barang::STATUS_TERSEDIA => ['label' => 'Tersedia', 'warna' => '#0ca30c', 'icon' => 'status-tersedia', 'teks' => 'text-emerald-700'],
    ];
    $persen = fn ($n) => $total > 0 ? round($n / $total * 100) : 0;
    $jumlahKritis = $ringkasan[Barang::STATUS_HABIS] + $ringkasan[Barang::STATUS_HAMPIR_HABIS];

    $dataGrafik = [
        'status' => collect($status)->map(fn ($s, $k) => ['kunci' => $k, 'label' => $s['label'], 'warna' => $s['warna'], 'jumlah' => $ringkasan[$k]])->values(),
        'kategori' => $perKategori->keys()->values(),
        'perKategori' => $perKategori->values(),
    ];
@endphp

@push('scripts')
    @vite('resources/js/report.js')
@endpush

<x-layouts.app title="Report">
    {{-- Filter: satu baris di atas, berlaku untuk semua grafik & tabel di halaman ini --}}
    <form method="GET" action="{{ route('report.index') }}" class="flex flex-wrap items-center gap-3 sm:gap-5">
        <select name="kategori" onchange="this.form.submit()" aria-label="Filter kategori"
            class="rounded-lg border border-slate-200 bg-white py-2.5 pr-9 pl-4 text-[15px] focus:outline-none">
            <option value="">Semua Kategori</option>
            @foreach ($kategoriList as $k)
                <option value="{{ $k }}" @selected($kategori === $k)>{{ $k }}</option>
            @endforeach
        </select>
        @if ($kategori)
            <a href="{{ route('report.index') }}" class="text-[15px] font-semibold text-brand-500 hover:text-brand-600">Reset Filter</a>
        @endif
        <noscript><button type="submit" class="btn btn-outline">Terapkan</button></noscript>
        <p class="text-sm text-slate-500 sm:ml-auto">Kondisi stok per {{ now()->translatedFormat('d M Y, H:i') }}</p>
    </form>

    <h2 class="mt-8 text-lg font-bold">Kesehatan Stok</h2>
    <p class="mt-1 text-sm text-slate-500">Status dihitung dari stok saat ini dibanding batas hampir habis tiap barang.</p>

    {{-- Ringkasan angka --}}
    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card p-5">
            <p class="text-sm font-medium text-slate-500">Total Barang</p>
            <p class="mt-2 text-3xl font-extrabold tracking-tight">{{ number_format($total) }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ $kategori ?? 'Semua kategori' }}</p>
        </div>
        @foreach ($status as $kunci => $s)
            <div class="card p-5">
                <p class="flex items-center gap-1.5 text-sm font-medium text-slate-500">
                    <x-icon :name="$s['icon']" class="size-4 {{ $s['teks'] }}" /> {{ $s['label'] }}
                </p>
                <p class="mt-2 text-3xl font-extrabold tracking-tight">{{ number_format($ringkasan[$kunci]) }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $persen($ringkasan[$kunci]) }}% dari total barang</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        {{-- Donat status stok --}}
        <section class="card p-6">
            <h3 class="font-bold">Komposisi Status Stok</h3>
            <p class="mt-1 text-sm text-slate-500">Proporsi barang menurut status.</p>

            <div class="relative mx-auto mt-6 aspect-square w-full max-w-[240px]">
                <canvas id="grafik-status" role="img"
                    aria-label="Donat status stok: {{ collect($status)->map(fn ($s, $k) => $s['label'].' '.$ringkasan[$k])->implode(', ') }}"></canvas>
                <div class="pointer-events-none absolute inset-0 grid place-content-center text-center">
                    <p class="text-4xl font-extrabold tracking-tight">{{ $persen($jumlahKritis) }}%</p>
                    <p class="text-xs text-slate-500">perlu diisi ulang</p>
                </div>
            </div>

            <ul class="mt-6 space-y-2 text-sm">
                @foreach ($status as $kunci => $s)
                    <li class="flex items-center gap-2">
                        <span class="size-2.5 shrink-0 rounded-full" style="background: {{ $s['warna'] }}"></span>
                        <x-icon :name="$s['icon']" class="size-4 shrink-0 {{ $s['teks'] }}" />
                        <span class="text-slate-700">{{ $s['label'] }}</span>
                        <span class="ml-auto font-semibold tabular-nums">{{ $ringkasan[$kunci] }}</span>
                        <span class="w-10 text-right text-slate-500 tabular-nums">{{ $persen($ringkasan[$kunci]) }}%</span>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Status per kategori --}}
        <section class="card p-6 lg:col-span-2">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h3 class="font-bold">Status Stok per Kategori</h3>
                    <p class="mt-1 text-sm text-slate-500">Diurutkan dari kategori dengan barang habis terbanyak.</p>
                </div>
                <ul class="flex flex-wrap gap-x-4 gap-y-1 text-sm">
                    @foreach ($status as $s)
                        <li class="flex items-center gap-1.5">
                            <span class="size-2.5 rounded-full" style="background: {{ $s['warna'] }}"></span>
                            <x-icon :name="$s['icon']" class="size-4 {{ $s['teks'] }}" />
                            <span class="text-slate-700">{{ $s['label'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="mt-4" style="height: {{ max(160, $perKategori->count() * 34 + 40) }}px">
                <canvas id="grafik-kategori" role="img" aria-label="Batang bertumpuk jumlah barang per status untuk tiap kategori"></canvas>
            </div>

            <details class="mt-4 text-sm">
                <summary class="cursor-pointer font-semibold text-navy-800">Lihat tabel</summary>
                <div class="mt-3 overflow-x-auto">
                    <table class="w-full">
                        <thead class="table-head">
                            <tr>
                                <th>Kategori</th>
                                @foreach ($status as $s)
                                    <th class="text-right">{{ $s['label'] }}</th>
                                @endforeach
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="table-body tabular-nums">
                            @foreach ($perKategori as $nama => $jumlah)
                                <tr>
                                    <td>{{ $nama }}</td>
                                    @foreach ($status as $kunci => $s)
                                        <td class="text-right">{{ $jumlah[$kunci] }}</td>
                                    @endforeach
                                    <td class="text-right font-semibold">{{ $jumlah->sum() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        </section>
    </div>

    {{-- Barang kritis --}}
    <section class="card mt-6">
        <div class="flex flex-wrap items-start justify-between gap-3 p-6 pb-4">
            <div>
                <h3 class="font-bold">Barang Kritis <span class="font-normal text-slate-500">({{ $kritis->count() }})</span></h3>
                <p class="mt-1 text-sm text-slate-500">Barang habis dan hampir habis, diurutkan dari stok paling sedikit. Meteran menunjukkan stok dibanding batas hampir habis.</p>
            </div>
        </div>

        <div class="max-h-[32rem] overflow-auto border-t border-slate-100">
            <table class="w-full">
                <thead class="table-head sticky top-0 z-10">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-right">Stok</th>
                        <th class="text-right">Batas</th>
                        <th>Ketersediaan</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    @forelse ($kritis as $b)
                        @php
                            $s = $status[$b->status];
                            $rasio = $b->stok_minimum > 0 ? min(1, $b->stok / $b->stok_minimum) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/60">
                            <td class="font-mono text-[13px] text-slate-500">{{ $b->kode_barang }}</td>
                            <td class="font-semibold">{{ $b->nama }}</td>
                            <td class="text-slate-600">{{ $b->kategori }}</td>
                            <td class="text-right font-bold tabular-nums">{{ number_format($b->stok) }} <span class="font-normal text-slate-500">{{ $b->satuan }}</span></td>
                            <td class="text-right text-slate-500 tabular-nums">{{ number_format($b->stok_minimum) }}</td>
                            <td>
                                <div class="flex items-center gap-2" title="{{ $b->stok }} dari batas {{ $b->stok_minimum }} {{ $b->satuan }}">
                                    <div class="h-1.5 w-24 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full" style="width: {{ $rasio * 100 }}%; background: {{ $s['warna'] }}"></div>
                                    </div>
                                    <span class="w-9 text-right text-xs text-slate-500 tabular-nums">{{ round($rasio * 100) }}%</span>
                                </div>
                            </td>
                            <td><x-status-badge :status="$b->status" /></td>
                            <td class="text-right">
                                <a href="{{ route('barang.index', ['q' => $b->kode_barang]) }}"
                                    class="text-sm font-semibold text-brand-500 hover:text-brand-600">Isi stok</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-500">Tidak ada barang kritis. Semua stok aman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <script type="application/json" id="data-report">@json($dataGrafik)</script>
</x-layouts.app>
