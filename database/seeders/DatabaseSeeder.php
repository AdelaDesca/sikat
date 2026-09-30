<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Albertus W.',
            'username' => 'admin',
            'email' => 'admin@bankpapua.local',
            'jabatan' => 'Logistik Papua',
            'kantor' => 'Kancab Jayapura',
            'password' => 'admin123',
        ]);

        // [nama, kategori, stok, satuan]
        $daftar = [
            ['Kertas HVS A4 Sinar Dunia 80gr', 'Kertas', 124, 'Rim'],
            ['Kertas HVS F4 Sinar Dunia 80gr', 'Kertas', 82, 'Rim'],
            ['Pulpen Pilot Ballpoint Black', 'Alat Tulis', 15, 'Pax'],
            ['Spidol Snowman Whiteboard Blue', 'Alat Tulis', 4, 'Pcs'],
            ['Stapler Max HD-10', 'Peralatan Kantor', 28, 'Unit'],
            ['Isi Staples Max No. 10', 'Peralatan Kantor', 0, 'Box'],
            ['Map Folder Plastik Blue', 'Penyimpanan', 240, 'Pcs'],
            ['Tinta Printer Epson T664 Black', 'Tinta Printer', 0, 'Botol'],
            ['Amplop Kabinet Royal', 'Kertas Surat', 45, 'Box'],
            ['Buku Kwitansi Paperline', 'Administrasi', 12, 'Buku'],
        ];

        $barang = [];
        foreach ($daftar as [$nama, $kategori, $stok, $satuan]) {
            $barang[$nama] = Barang::create(compact('nama', 'kategori', 'stok', 'satuan') + ['stok_minimum' => 20]);
        }

        // Riwayat contoh; [hari lalu, jam, barang, jenis, jumlah, oleh, keterangan, status]
        $riwayat = [
            [0, '09:15', 'Kertas HVS A4 Sinar Dunia 80gr', 'keluar', 10, 'Albertus W.', 'Distribusi ke Div. Kredit', 'selesai'],
            [1, '14:30', 'Pulpen Pilot Ballpoint Black', 'keluar', 2, 'Dewi Papua', 'Kebutuhan Front Office', 'selesai'],
            [1, '10:10', 'Tinta Printer Epson T664 Black', 'permintaan', 6, 'Albertus W.', 'Stok tinta habis', 'pending'],
            [2, '11:00', 'Isi Staples Max No. 10', 'permintaan', 5, 'Albertus W.', 'Restock gudang pusat', 'pending'],
            [2, '10:45', 'Isi Staples Max No. 10', 'keluar', 1, 'Fransiska K.', 'Divisi SDM & Diklat', 'selesai'],
            [3, '09:00', 'Spidol Snowman Whiteboard Blue', 'permintaan', 24, 'Albertus W.', 'Persiapan pelatihan', 'pending'],
            [4, '16:00', 'Tinta Printer Epson T664 Black', 'keluar', 2, 'Budi Santoso', 'Sekretaris Direksi', 'selesai'],
            [5, '08:30', 'Map Folder Plastik Blue', 'keluar', 50, 'Albertus W.', 'Persiapan rapat pleno', 'selesai'],
            [6, '13:15', 'Kertas HVS F4 Sinar Dunia 80gr', 'masuk', 50, 'Suplai Vendor', 'Pengadaan rutin bulanan', 'selesai'],
            [7, '15:40', 'Spidol Snowman Whiteboard Blue', 'keluar', 2, 'Michael P.', 'Kelas training Bank', 'selesai'],
            [9, '10:00', 'Stapler Max HD-10', 'keluar', 3, 'Yuliana M.', 'Cabang pembantu Sentani', 'selesai'],
            [10, '11:20', 'Amplop Kabinet Royal', 'keluar', 2, 'Albertus W.', 'Surat menyurat Div. Umum', 'selesai'],
            [12, '09:30', 'Map Folder Plastik Blue', 'masuk', 200, 'Suplai Vendor', 'Pengadaan triwulan', 'selesai'],
        ];

        foreach ($riwayat as [$hari, $jam, $nama, $jenis, $jumlah, $oleh, $keterangan, $status]) {
            Transaksi::create([
                'barang_id' => $barang[$nama]->id,
                'user_id' => $oleh === $admin->name ? $admin->id : null,
                'jenis' => $jenis,
                'jumlah' => $jumlah,
                'oleh' => $oleh,
                'keterangan' => $keterangan,
                'status' => $status,
                'tanggal' => now()->subDays($hari)->setTimeFromTimeString($jam),
            ]);
        }
    }
}
