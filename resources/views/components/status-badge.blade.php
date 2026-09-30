@props(['status'])

@php
    [$label, $kelas] = match ($status) {
        \App\Models\Barang::STATUS_HABIS => ['Habis', 'bg-red-100 text-red-600'],
        \App\Models\Barang::STATUS_HAMPIR_HABIS => ['Hampir Habis', 'bg-amber-100 text-amber-600'],
        default => ['Tersedia', 'bg-blue-100 text-blue-600'],
    };
@endphp

<span {{ $attributes->class(['badge', $kelas]) }}>{{ $label }}</span>
