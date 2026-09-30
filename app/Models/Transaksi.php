<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['barang_id', 'user_id', 'jenis', 'jumlah', 'oleh', 'keterangan', 'status', 'tanggal'])]
class Transaksi extends Model
{
    public const JENIS_MASUK = 'masuk';
    public const JENIS_KELUAR = 'keluar';
    public const JENIS_PERMINTAAN = 'permintaan';

    public const STATUS_SELESAI = 'selesai';
    public const STATUS_PENDING = 'pending';
    public const STATUS_DITOLAK = 'ditolak';

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tanggal' => 'datetime',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Masuk & permintaan menambah stok, keluar mengurangi stok.
     */
    public function isPenambahan(): bool
    {
        return $this->jenis !== self::JENIS_KELUAR;
    }
}
