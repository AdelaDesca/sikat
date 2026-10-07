<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kode_barang', 'jenis', 'jumlah', 'oleh', 'keterangan', 'tanggal'])]
class Transaksi extends Model
{
    public const JENIS_MASUK = 'masuk';
    public const JENIS_KELUAR = 'keluar';
    public const JENIS_KOREKSI = 'koreksi'; // jumlah = selisih stok (+/-), bukan pemakaian

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tanggal' => 'datetime',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'kode_barang');
    }

    /**
     * Masuk menambah stok, keluar mengurangi stok, koreksi mengikuti tanda selisihnya.
     */
    public function isPenambahan(): bool
    {
        return $this->jenis === self::JENIS_MASUK
            || ($this->jenis === self::JENIS_KOREKSI && $this->jumlah > 0);
    }
}
