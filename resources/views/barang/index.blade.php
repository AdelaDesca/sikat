<x-layouts.app title="Stok Barang ATK">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <form method="GET" action="{{ route('barang.index') }}" class="relative w-full sm:max-w-sm">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-[18px] -translate-y-1/2 text-slate-500" />
            <input type="search" name="q" value="{{ $cari }}" placeholder="Cari nama ATK..."
                class="form-input py-3 pl-11">
        </form>

        <div class="flex gap-3 sm:ml-auto">
            <button type="button" data-open="modal-minta" class="btn btn-outline flex-1 py-3 sm:flex-none">
                <x-icon name="download" class="size-[18px]" /> Minta ATK
            </button>
            <button type="button" data-open="modal-tambah" class="btn btn-primary flex-1 py-3 sm:flex-none">
                <x-icon name="plus" class="size-[18px]" /> Tambah ATK Baru
            </button>
        </div>
    </div>

    <div class="card mt-6 overflow-x-auto">
        <table class="w-full">
            <thead class="table-head">
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse ($barangs as $b)
                    <tr class="hover:bg-slate-50/60">
                        <td class="text-slate-500">{{ str_pad($barangs->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="font-semibold">{{ $b->nama }}</td>
                        <td class="text-slate-600">{{ $b->kategori }}</td>
                        <td class="font-bold">{{ number_format($b->stok) }}</td>
                        <td class="text-slate-500">{{ $b->satuan }}</td>
                        <td><x-status-badge :status="$b->status" /></td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <button type="button" title="Barang masuk" data-open="modal-transaksi"
                                    data-field-barang-id="{{ $b->id }}" data-field-jenis="masuk"
                                    data-text-judul="Barang Masuk" data-text-barang="{{ $b->nama }}"
                                    data-text-stok="{{ $b->stok }} {{ $b->satuan }}"
                                    class="cursor-pointer rounded-lg p-2 text-emerald-600 hover:bg-emerald-50">
                                    <x-icon name="arrow-down" class="size-4" />
                                </button>
                                <button type="button" title="Barang keluar" data-open="modal-transaksi"
                                    data-field-barang-id="{{ $b->id }}" data-field-jenis="keluar"
                                    data-text-judul="Barang Keluar" data-text-barang="{{ $b->nama }}"
                                    data-text-stok="{{ $b->stok }} {{ $b->satuan }}"
                                    @disabled($b->stok == 0)
                                    class="cursor-pointer rounded-lg p-2 text-amber-600 hover:bg-amber-50 disabled:cursor-not-allowed disabled:opacity-30">
                                    <x-icon name="arrow-up" class="size-4" />
                                </button>
                                <button type="button" title="Ubah" data-open="modal-edit"
                                    data-action="{{ route('barang.update', $b) }}"
                                    data-field-edit-id="{{ $b->id }}" data-field-nama="{{ $b->nama }}"
                                    data-field-kategori="{{ $b->kategori }}" data-field-satuan="{{ $b->satuan }}"
                                    data-field-stok-minimum="{{ $b->stok_minimum }}"
                                    class="cursor-pointer rounded-lg p-2 text-navy-800 hover:bg-navy-50">
                                    <x-icon name="pencil" class="size-4" />
                                </button>
                                <button type="button" title="Hapus" data-open="modal-hapus"
                                    data-action="{{ route('barang.destroy', $b) }}" data-text-barang="{{ $b->nama }}"
                                    class="cursor-pointer rounded-lg p-2 text-red-600 hover:bg-red-50">
                                    <x-icon name="trash" class="size-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-500">
                            {{ $cari ? "Tidak ada ATK yang cocok dengan \"{$cari}\"." : 'Belum ada data ATK.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $barangs->links() }}</div>

    {{-- Modal: Tambah ATK Baru --}}
    <x-modal id="modal-tambah" title="Tambah ATK Baru">
        <form method="POST" action="{{ route('barang.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="modal-tambah">
            @include('barang._form', ['stokAwal' => true, 'aktif' => old('_modal') === 'modal-tambah'])
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" data-close class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal: Ubah ATK --}}
    <x-modal id="modal-edit" title="Ubah Data ATK">
        <form method="POST" action="{{ old('edit_id') ? route('barang.update', old('edit_id')) : '#' }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="_modal" value="modal-edit">
            <input type="hidden" name="edit_id" value="{{ old('edit_id') }}">
            @include('barang._form', ['stokAwal' => false, 'aktif' => old('_modal') === 'modal-edit'])
            <p class="text-xs text-slate-500">Jumlah stok diubah melalui tombol barang masuk / keluar agar tercatat di riwayat.</p>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" data-close class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal: Barang masuk / keluar --}}
    @php $aktifTrx = old('_modal') === 'modal-transaksi'; @endphp
    <x-modal id="modal-transaksi" title="">
        <form method="POST" action="{{ route('transaksi.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_modal" value="modal-transaksi">
            <input type="hidden" name="barang_id" value="{{ $aktifTrx ? old('barang_id') : '' }}">
            <input type="hidden" name="jenis" value="{{ $aktifTrx ? old('jenis') : '' }}">

            <div class="-mt-2 rounded-lg bg-slate-50 px-4 py-3">
                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase" data-slot="judul">
                    {{ $aktifTrx && old('jenis') === 'keluar' ? 'Barang Keluar' : 'Barang Masuk' }}
                </p>
                <p class="mt-0.5 font-bold" data-slot="barang">
                    {{ $aktifTrx ? $semuaBarang->firstWhere('id', old('barang_id'))?->nama : '' }}
                </p>
                <p class="mt-0.5 text-sm text-slate-500">Stok saat ini: <span data-slot="stok">
                    @if ($aktifTrx && ($bOld = $semuaBarang->firstWhere('id', old('barang_id'))))
                        {{ $bOld->stok }} {{ $bOld->satuan }}
                    @endif
                </span></p>
            </div>

            <div>
                <label class="form-label" for="trx-jumlah">Jumlah</label>
                <input id="trx-jumlah" type="number" name="jumlah" min="1" required class="form-input"
                    value="{{ $aktifTrx ? old('jumlah') : '' }}">
                @if ($aktifTrx) @error('jumlah') <p class="form-error">{{ $message }}</p> @enderror @endif
            </div>
            <div>
                <label class="form-label" for="trx-oleh">Oleh</label>
                <input id="trx-oleh" type="text" name="oleh" required class="form-input"
                    value="{{ $aktifTrx ? old('oleh') : auth()->user()->name }}"
                    placeholder="Nama pegawai / vendor">
                @if ($aktifTrx) @error('oleh') <p class="form-error">{{ $message }}</p> @enderror @endif
            </div>
            <div>
                <label class="form-label" for="trx-ket">Keterangan</label>
                <input id="trx-ket" type="text" name="keterangan" class="form-input"
                    value="{{ $aktifTrx ? old('keterangan') : '' }}"
                    placeholder="mis. Distribusi ke Div. Kredit">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" data-close class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </x-modal>

    @include('barang._modal-minta')

    {{-- Modal: Hapus --}}
    <x-modal id="modal-hapus" title="Hapus ATK">
        <form method="POST" action="#">
            @csrf
            @method('DELETE')
            <p class="text-[15px] text-slate-600">
                Yakin ingin menghapus <b data-slot="barang" class="text-ink"></b>? Seluruh riwayat transaksi barang ini juga akan terhapus.
            </p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" data-close class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
