@props(['id', 'title'])

{{-- Modal memakai elemen <dialog> bawaan browser. Buka dengan data-open="{{ $id }}". --}}
<dialog id="{{ $id }}" {{ $attributes->class(['modal']) }}>
    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
        <h3 class="text-lg font-bold">{{ $title }}</h3>
        <button type="button" data-close class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
            aria-label="Tutup">
            <x-icon name="x" />
        </button>
    </div>
    <div class="px-6 py-5">
        {{ $slot }}
    </div>
</dialog>
