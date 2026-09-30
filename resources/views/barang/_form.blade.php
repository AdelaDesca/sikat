{{-- Field ATK dipakai bersama oleh modal tambah & ubah. $aktif = modal ini yang gagal validasi. --}}
@php $nilai = fn ($kolom, $default = '') => $aktif ? old($kolom, $default) : $default; @endphp

<div>
    <label class="form-label">Nama Barang</label>
    <input type="text" name="nama" required class="form-input" value="{{ $nilai('nama') }}"
        placeholder="mis. Kertas HVS A4 Sinar Dunia 80gr">
    @if ($aktif) @error('nama') <p class="form-error">{{ $message }}</p> @enderror @endif
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="form-label">Kategori</label>
        <input type="text" name="kategori" required list="daftar-kategori" class="form-input"
            value="{{ $nilai('kategori') }}" placeholder="Pilih / ketik kategori">
        @if ($aktif) @error('kategori') <p class="form-error">{{ $message }}</p> @enderror @endif
    </div>
    <div>
        <label class="form-label">Satuan</label>
        <input type="text" name="satuan" required list="daftar-satuan" class="form-input"
            value="{{ $nilai('satuan') }}" placeholder="Rim, Pcs, Box...">
        @if ($aktif) @error('satuan') <p class="form-error">{{ $message }}</p> @enderror @endif
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    @if ($stokAwal)
        <div>
            <label class="form-label">Stok Awal</label>
            <input type="number" name="stok" min="0" required class="form-input" value="{{ $nilai('stok', 0) }}">
            @if ($aktif) @error('stok') <p class="form-error">{{ $message }}</p> @enderror @endif
        </div>
    @endif
    <div>
        <label class="form-label">Batas Hampir Habis</label>
        <input type="number" name="stok_minimum" min="0" required class="form-input"
            value="{{ $nilai('stok_minimum', 20) }}">
        @if ($aktif) @error('stok_minimum') <p class="form-error">{{ $message }}</p> @enderror @endif
    </div>
</div>

@once
    <datalist id="daftar-kategori">
        @foreach ($kategoriList as $k)
            <option value="{{ $k }}"></option>
        @endforeach
    </datalist>
    <datalist id="daftar-satuan">
        @foreach ($satuanList as $s)
            <option value="{{ $s }}"></option>
        @endforeach
    </datalist>
@endonce
