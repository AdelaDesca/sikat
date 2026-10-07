@props(['title' => null])
@php
    $menu = [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
        ['route' => 'barang.index', 'label' => 'Stok Barang', 'icon' => 'archive'],
        ['route' => 'riwayat.index', 'label' => 'Riwayat Barang', 'icon' => 'history'],
    ];
    $user = auth()->user();
    $inisial = mb_strtoupper(mb_substr($user->username, 0, 1));
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} · SIKAT Bank Papua</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body data-reopen="{{ $errors->any() ? old('_modal') : '' }}">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <input type="checkbox" id="nav-toggle" class="peer hidden">
        <label for="nav-toggle" class="fixed inset-0 z-30 hidden bg-slate-900/40 peer-checked:block lg:peer-checked:hidden"></label>
        <aside
            class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform peer-checked:translate-x-0 lg:translate-x-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-7 pt-7 pb-8">
                <span class="leading-tight">
                    <span class="block text-[20px] font-black tracking-wider text-navy-800" style="font-family: 'Cinzel Decorative', fantasy;">SIKAT</span>
                    <span class="block text-[10px] font-semibold tracking-wide text-brand-500">SISTEM KELOLA ATK</span>
                </span>
            </a>

            <nav class="space-y-1.5 px-4">
                @foreach ($menu as $item)
                    @php $aktif = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}" @class([
                        'relative flex items-center gap-3 rounded-lg px-4 py-2.5 text-[15px] transition',
                        'bg-brand-50 font-semibold text-brand-500' => $aktif,
                        'text-slate-700 hover:bg-slate-50' => !$aktif,
                    ])>
                        <x-icon :name="$item['icon']" @class(['size-[18px]', 'text-slate-500' => !$aktif]) />
                        {{ $item['label'] }}
                        @if ($aktif)
                            <span class="absolute top-1/2 right-4 h-4 w-1 -translate-y-1/2 rounded-full bg-brand-500"></span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="mx-4 mt-auto mb-5 border-t border-slate-200 pt-4">
                <div class="flex items-center gap-3 px-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-full bg-navy-800 text-sm font-bold text-white">
                        {{ $inisial }}
                    </span>
                    <div class="min-w-0 flex-1 leading-tight">
                        <p class="truncate text-[13px] font-bold">{{ $user->name }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Keluar"
                            class="cursor-pointer rounded-lg p-2 text-slate-400 hover:bg-red-50 hover:text-red-600">
                            <x-icon name="logout" class="size-4" />
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Konten --}}
        <div class="flex min-w-0 flex-1 flex-col lg:pl-64">
            <header class="sticky top-0 z-20 flex h-[72px] items-center gap-3 border-b border-slate-200 bg-white px-4 sm:px-8">
                <label for="nav-toggle" class="cursor-pointer rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden">
                    <x-icon name="menu" />
                </label>
                <h1 class="truncate text-lg font-bold sm:text-[21px]">{{ $title ?? '' }}</h1>

                <div class="ml-auto flex items-center gap-3">
                    <details data-dropdown class="relative">
                        <summary
                            class="relative grid size-10 cursor-pointer list-none place-items-center rounded-full border border-slate-200 text-navy-800 hover:bg-slate-50 [&::-webkit-details-marker]:hidden">
                            <x-icon name="bell" class="size-[18px]" />
                            @if ($stokMenipis->isNotEmpty())
                                <span class="absolute top-1.5 right-2 size-2 rounded-full bg-brand-500 ring-2 ring-white"></span>
                            @endif
                        </summary>
                        <div class="absolute right-0 z-30 mt-2 w-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                            <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 text-sm">
                                <span class="font-bold">Notifikasi</span>
                                @if ($stokMenipis->isNotEmpty())
                                    <span class="text-xs text-slate-500">
                                        {{ $stokMenipis->where('stok', 0)->count() }} habis ·
                                        {{ $stokMenipis->where('stok', '>', 0)->count() }} hampir habis
                                    </span>
                                @endif
                            </div>
                            <div class="max-h-96 overflow-y-auto text-sm">
                                @forelse ($stokMenipis as $b)
                                    <a href="{{ route('barang.index', ['q' => $b->kode_barang]) }}" class="flex gap-3 px-4 py-3 hover:bg-slate-50">
                                        <x-icon name="alert" @class(['mt-0.5 size-4 shrink-0', 'text-red-500' => $b->stok == 0, 'text-amber-500' => $b->stok > 0]) />
                                        <span><b>{{ $b->nama }}</b> {{ $b->stok == 0 ? 'habis' : "tersisa {$b->stok} {$b->satuan}" }}</span>
                                    </a>
                                @empty
                                    <p class="px-4 py-6 text-center text-slate-500">Tidak ada notifikasi.</p>
                                @endforelse
                            </div>
                        </div>
                    </details>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-8">
                @if (session('success'))
                    <div data-flash class="mb-6 flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        <x-icon name="check" class="size-4" /> {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any() && !old('_modal'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
