# SIKAT — Sistem Kelola ATK Bank Papua

Aplikasi Laravel (Blade + Tailwind CSS) untuk mengelola stok ATK: dashboard, stok barang, dan riwayat keluar/masuk.

## Menjalankan

Proyek ini ada di folder Herd, jadi langsung bisa dibuka di **http://sikat.test**.

Login awal: `admin` / `admin123` (segera ganti di production).

```bash
composer install
npm install && npm run build     # atau `npm run dev` saat mengubah tampilan
php artisan migrate:fresh --seed # reset database + data contoh
php artisan test                 # jalankan pengujian
```

## Alur stok

| Aksi | Tempat | Efek ke stok |
|---|---|---|
| Tambah ATK Baru | Stok Barang | Stok awal dicatat sebagai transaksi **Masuk** |
| Barang masuk (↓) / keluar (↑) | Stok Barang, kolom Aksi | Langsung menambah / mengurangi (keluar tidak boleh melebihi stok) |
| Minta ATK | Stok Barang | Transaksi **Permintaan** berstatus pending, stok belum berubah |
| Setujui / Tolak permintaan | Riwayat Barang | Setujui menambah stok; tolak tidak mengubah stok |

Status stok dihitung otomatis: **Habis** (0), **Hampir Habis** (≤ batas minimum, default 20), **Tersedia**.

## Pakai MySQL (XAMPP)

Buat database `sikat` di phpMyAdmin, lalu ubah `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sikat
DB_USERNAME=root
DB_PASSWORD=
```

Kemudian jalankan `php artisan migrate:fresh --seed`.
