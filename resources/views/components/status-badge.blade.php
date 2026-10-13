@props(['status'])

@php
    [$label, $kelas] = match ($status) {
        \App\Models\Barang::STATUS_HABIS => ['Habis', 'bg-red-100 text-red-600'],
        \App\Models\Barang::STATUS_HAMPIR_HABIS => ['Hampir Habis', 'bg-amber-100 text-amber-600'],
        default => ['Tersedia', 'bg-emerald-100 text-emerald-700'],
    };
@endphp

<span {{ $attributes->class(['badge', $kelas]) }}>{{ $label }}</span>
