<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode_barang', 'nama', 'kategori', 'stok', 'satuan', 'stok_minimum'])]
class Barang extends Model
{
    protected $primaryKey = 'kode_barang';

    public $incrementing = false;

    protected $keyType = 'string';

    public const STATUS_TERSEDIA = 'tersedia';
    public const STATUS_HAMPIR_HABIS = 'hampir_habis';
    public const STATUS_HABIS = 'habis';

    public const KATEGORI = [
        'Alat Tulis',
        'Amplop',
        'Buku Tabungan',
        'Formulir & Aplikasi',
        'Kertas',
        'Klip & Staples',
        'Label & Stiker',
        'Map & Penyimpanan',
        'Perekat',
        'Peralatan Kantor',
        'Tinta & Pita',
        'Lain-lain',
    ];

    public const SATUAN = ['Pcs', 'Lembar', 'Buku', 'Rim', 'Box', 'Pax', 'Roll', 'Unit', 'Botol', 'Lusin', 'Set'];

    protected function casts(): array
    {
        return [
            'stok' => 'integer',
            'stok_minimum' => 'integer',
        ];
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'kode_barang');
    }

    /**
     * Status stok dihitung dari jumlah stok terhadap batas minimum.
     */
    protected function status(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            $this->stok <= 0 => self::STATUS_HABIS,
            $this->stok <= $this->stok_minimum => self::STATUS_HAMPIR_HABIS,
            default => self::STATUS_TERSEDIA,
        });
    }

    /**
     * Semua kategori: daftar bawaan ditambah kategori yang sudah dipakai di database.
     *
     * @return array<int, string>
     */
    public static function daftarKategori(): array
    {
        return collect(self::KATEGORI)
            ->merge(self::query()->distinct()->pluck('kategori'))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
