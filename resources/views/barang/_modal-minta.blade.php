{{-- Modal: Minta ATK (permintaan restock, stok bertambah setelah disetujui) --}}
@php $aktifMinta = old('_modal') === 'modal-minta'; @endphp

<x-modal id="modal-minta" title="Minta ATK">
    <form method="POST" action="{{ route('permintaan.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="_modal" value="modal-minta">
        <div>
            <label class="form-label" for="minta-barang">Barang</label>
            <select id="minta-barang" name="barang_id" required class="form-input">
                <option value="">— Pilih ATK —</option>
                @foreach ($semuaBarang as $b)
                    <option value="{{ $b->id }}" @selected($aktifMinta && old('barang_id') == $b->id)>
                        {{ $b->nama }} (stok {{ $b->stok }} {{ $b->satuan }})
                    </option>
                @endforeach
            </select>
            @if ($aktifMinta) @error('barang_id') <p class="form-error">{{ $message }}</p> @enderror @endif
        </div>
        <div>
            <label class="form-label" for="minta-jumlah">Jumlah</label>
            <input id="minta-jumlah" type="number" name="jumlah" min="1" required class="form-input"
                value="{{ $aktifMinta ? old('jumlah') : '' }}">
            @if ($aktifMinta) @error('jumlah') <p class="form-error">{{ $message }}</p> @enderror @endif
        </div>
        <div>
            <label class="form-label" for="minta-ket">Keterangan</label>
            <input id="minta-ket" type="text" name="keterangan" class="form-input"
                value="{{ $aktifMinta ? old('keterangan') : '' }}" placeholder="mis. Restock gudang pusat">
        </div>
        <p class="text-xs text-slate-500">Permintaan akan berstatus pending. Stok bertambah setelah permintaan disetujui di menu Riwayat Barang.</p>
        <div class="flex justify-end gap-3 pt-2">
            <button type="button" data-close class="btn btn-outline">Batal</button>
            <button type="submit" class="btn btn-primary">Ajukan Permintaan</button>
        </div>
    </form>
</x-modal>
