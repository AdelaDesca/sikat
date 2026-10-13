<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;

/**
 * Daftar ATK Bank Papua Cabang Yogyakarta (sumber: Stok Barang ATK 11 Agustus 2026).
 * Kode yang dipakai lebih dari satu barang di dokumen asli diberi akhiran huruf (F007A, F007B, ...).
 * Stok awal 0; stok diisi lewat transaksi "Barang masuk".
 */
class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // [kode, nama, kategori, satuan]
        $daftar = [
            ['A001', 'Amplop Kop Bank Papua', 'Amplop', 'Pcs'],
            ['A002', 'Amplop Coklat Kecil', 'Amplop', 'Pcs'],
            ['A003', 'Amplop Coklat 310', 'Amplop', 'Pcs'],
            ['A004', 'Amplop Putih 152x90 mm', 'Amplop', 'Pcs'],
            ['A005', 'Amplop Putih 110x230 mm', 'Amplop', 'Pcs'],
            ['A006', 'Aplikasi SMS Banking', 'Formulir & Aplikasi', 'Buku'],
            ['A007', 'Aplikasi Penarikan/Setoran/Pemindahbukuan (1 Lembar)', 'Formulir & Aplikasi', 'Buku'],
            ['A008', 'Aplikasi Penarikan/Setoran/Pemindahbukuan (2 Lembar)', 'Formulir & Aplikasi', 'Buku'],
            ['A009', 'Aplikasi Transfer', 'Formulir & Aplikasi', 'Buku'],

            ['B001', 'Bantex Pocket Bening', 'Map & Penyimpanan', 'Pcs'],
            ['B002', 'Binder Clips No. 155', 'Klip & Staples', 'Box'],
            ['B003', 'Binder Clips DB 105', 'Klip & Staples', 'Box'],
            ['B004', 'Benang Rajut', 'Lain-lain', 'Pcs'],
            ['B005', 'Binder Clips DB E-260', 'Klip & Staples', 'Pcs'],
            ['B006', 'Baterai ABC', 'Peralatan Kantor', 'Pcs'],
            ['B007', 'Bindeks Tempel', 'Label & Stiker', 'Buku'],
            ['B008', 'Buku Tabunganku', 'Buku Tabungan', 'Pax'],
            ['B009', 'Buku Tabungan Simpel', 'Buku Tabungan', 'Pax'],
            ['B010', 'Buku Tabungan Simanja', 'Buku Tabungan', 'Pax'],
            ['B011', 'Buku Tabungan Simpeda', 'Buku Tabungan', 'Pax'],
            ['B012', 'Bukti Pengeluaran Kas', 'Formulir & Aplikasi', 'Buku'],
            ['B013', 'Binder Clips No. 107', 'Klip & Staples', 'Box'],
            ['B014', 'Binder Clips No. 111', 'Klip & Staples', 'Box'],
            ['B015', 'Buku Folio Besar', 'Alat Tulis', 'Buku'],
            ['B016', 'Buku Folio Kecil (1/2) (1/4)', 'Alat Tulis', 'Buku'],
            ['B017', 'Bolpoint', 'Alat Tulis', 'Pcs'],
            ['B018', 'Bantex Box File', 'Map & Penyimpanan', 'Pcs'],
            ['B019', 'Bantex Clipboard', 'Map & Penyimpanan', 'Pcs'],
            ['B020', 'Bolpoint Standar', 'Alat Tulis', 'Pcs'],
            ['B021', 'Binder Clips DB 200', 'Klip & Staples', 'Box'],
            ['B022', 'Binder Clips DB 280', 'Klip & Staples', 'Box'],
            ['B023', 'Bisnis File F4', 'Map & Penyimpanan', 'Pcs'],
            ['B024', 'Bantex Hitam File', 'Map & Penyimpanan', 'Pcs'],

            ['C001A', 'Cable Ties Kepala Lebar', 'Peralatan Kantor', 'Pax'],
            ['C001B', 'Cable Ties Biasa', 'Peralatan Kantor', 'Pax'],
            ['C002', 'CD-R', 'Peralatan Kantor', 'Pcs'],
            ['C003', 'Cutter Sedang', 'Alat Tulis', 'Pcs'],
            ['C004', 'Cutter Besar', 'Alat Tulis', 'Pcs'],
            ['C005', 'Cutter Kecil', 'Alat Tulis', 'Pcs'],

            ['D001', 'Display Book', 'Map & Penyimpanan', 'Pcs'],
            ['D002', 'Double Tape Foam', 'Perekat', 'Pcs'],
            ['D003', 'Double Tape White', 'Perekat', 'Pcs'],
            ['D004', 'DVD-R', 'Peralatan Kantor', 'Pcs'],
            ['D005', 'Telly Doscom (Epson)', 'Lain-lain', 'Pcs'],
            ['D006', 'Double Tape Mounting', 'Perekat', 'Pcs'],
            ['D007', 'Double Side Mounting Tape', 'Perekat', 'Pcs'],

            ['E001', 'Epson Ribbon Cartridge PLQ-20, 20D', 'Tinta & Pita', 'Botol'],
            ['E002', 'Epson Ribbon Cartridge LX-310', 'Tinta & Pita', 'Botol'],

            ['F001', 'Formulir Mobile Banking', 'Formulir & Aplikasi', 'Buku'],
            ['F002', 'Form Permohonan Pembukaan Rekening', 'Formulir & Aplikasi', 'Buku'],
            ['F003', 'Form Permohonan Pembukaan Rekening Simpel', 'Formulir & Aplikasi', 'Buku'],
            ['F004', 'Formulir Pengaduan Nasabah', 'Formulir & Aplikasi', 'Buku'],
            ['F005', 'Formulir Permohonan Kartu ATM', 'Formulir & Aplikasi', 'Buku'],
            ['F006', 'Formulir Pendaftaran Merchant', 'Formulir & Aplikasi', 'Buku'],
            ['F007A', 'Lampiran Form (3 Lembar)', 'Formulir & Aplikasi', 'Buku'],
            ['F007B', 'Lampiran Form (1 Lembar)', 'Formulir & Aplikasi', 'Buku'],
            ['F008', 'Form Pengambilan Kartu ATM yang Tertelan', 'Formulir & Aplikasi', 'Buku'],
            ['F009', 'Form Laporan Kehilangan Kartu ATM Bank Papua', 'Formulir & Aplikasi', 'Buku'],
            ['F010', 'Form Customer Due Diligence Perorangan', 'Formulir & Aplikasi', 'Buku'],
            ['F011', 'Form Customer Due Diligence Korporasi', 'Formulir & Aplikasi', 'Buku'],

            ['G001', 'Glue Stick', 'Perekat', 'Pcs'],

            ['I001', 'Isolasi Bening Kecil', 'Perekat', 'Pcs'],
            ['I002', 'Isolasi Coklat Sedang', 'Perekat', 'Pcs'],
            ['I003', 'Isi Staples Sedang', 'Klip & Staples', 'Box'],
            ['I004', 'Isi Staples Kecil', 'Klip & Staples', 'Box'],
            ['I005', 'Isi Staples Besar', 'Klip & Staples', 'Box'],
            ['I006', 'Isi Bolpoint', 'Alat Tulis', 'Pcs'],
            ['I007', 'Isolasi Kabel (Unibel)', 'Perekat', 'Pcs'],
            ['I008', 'Isi Cutter Besar', 'Alat Tulis', 'Pcs'],
            ['I010', 'Isolasi Bening Sedang', 'Perekat', 'Pcs'],
            ['I011', 'Isolasi Bening Besar', 'Perekat', 'Pcs'],

            ['K001', 'Kertas Karbon Replacement', 'Kertas', 'Pcs'],
            ['K002', 'Kertas A4', 'Kertas', 'Rim'],
            ['K003', 'Kertas ATM', 'Kertas', 'Buku'],
            ['K004', 'Kalkulator Casio', 'Peralatan Kantor', 'Pcs'],
            ['K005', 'Kertas Kop', 'Kertas', 'Rim'],
            ['K006', 'Kwitansi', 'Formulir & Aplikasi', 'Buku'],
            ['K007', 'Kertas Sertifikat Bank Papua', 'Kertas', 'Lembar'],
            ['K008', 'Kertas Sertifikat Polos', 'Kertas', 'Lembar'],
            ['K009', 'Kwitansi Seri K', 'Formulir & Aplikasi', 'Buku'],
            ['K010', 'Kuas', 'Lain-lain', 'Pcs'],
            ['K011A', 'Sticker Matte Photo Paper', 'Kertas', 'Lembar'],
            ['K011B', 'Photo Paper Waterproof Glossy', 'Kertas', 'Lembar'],
            ['K011C', 'Transparent Sticker Paper', 'Kertas', 'Lembar'],
            ['K011D', 'Silky Photo Paper', 'Kertas', 'Lembar'],
            ['K012', 'Kertas Kas', 'Kertas', 'Buku'],
            ['K13', 'Kertas HVS F4', 'Kertas', 'Rim'],
            ['K013', 'Kertas Stiker Photo', 'Kertas', 'Lembar'],
            ['K014', 'Kenko Paper Clips No. 5 (Jumbo)', 'Klip & Staples', 'Box'],
            ['K015', 'Kertas Edisi Roll', 'Kertas', 'Roll'],
            ['K016', 'Kartu Stock', 'Kertas', 'Lembar'],
            ['K017', 'Kertas Termal Nomor Antrian', 'Kertas', 'Roll'],

            ['L001', 'Lakban Bening Besar', 'Perekat', 'Pcs'],
            ['L002', 'Label Uang Rp 100.000', 'Label & Stiker', 'Pax'],
            ['L003', 'Lakban Hitam', 'Perekat', 'Pcs'],
            ['L004', 'Label Uang Rp 50.000', 'Label & Stiker', 'Pax'],
            ['L005', 'Label Uang Rp 20.000', 'Label & Stiker', 'Pax'],
            ['L006', 'Label Uang Rp 10.000', 'Label & Stiker', 'Pax'],
            ['L007', 'Label Uang Rp 5.000', 'Label & Stiker', 'Pax'],
            ['L008', 'Label Uang Rp 2.000', 'Label & Stiker', 'Pax'],
            ['L009', 'Label Uang Rp 1.000', 'Label & Stiker', 'Pax'],
            ['L010', 'Lakban Coklat Besar', 'Perekat', 'Pcs'],
            ['L011', 'Lakban Kertas Besar', 'Perekat', 'Pcs'],
            ['L012', 'Lakban Kertas', 'Perekat', 'Pcs'],
            ['L013', 'Lakban Bening Sedang', 'Perekat', 'Pcs'],
            ['L014', 'Lakban Coklat Sedang', 'Perekat', 'Pcs'],

            ['M001', 'Map', 'Map & Penyimpanan', 'Pcs'],
            ['M002', 'Minyak Mesin', 'Lain-lain', 'Pcs'],
            ['M003', 'Map Album', 'Map & Penyimpanan', 'Pcs'],
            ['M004', 'Money Detector', 'Peralatan Kantor', 'Pcs'],
            ['M005A', 'Map Batik', 'Map & Penyimpanan', 'Pcs'],
            ['M005B', 'Mousepad', 'Peralatan Kantor', 'Pcs'],

            ['N001', 'Nota', 'Formulir & Aplikasi', 'Buku'],
            ['O001', 'Ordner Folio', 'Map & Penyimpanan', 'Pcs'],

            ['P001', 'Plastik Bening', 'Lain-lain', 'Pcs'],
            ['P002', 'Plastik Kresek Hitam', 'Lain-lain', 'Pcs'],
            ['P003', 'Povinal Liquid', 'Perekat', 'Botol'],
            ['P004', 'Penghapus', 'Alat Tulis', 'Pcs'],
            ['P005', 'Penggaris', 'Alat Tulis', 'Pcs'],
            ['P006', 'Papan Nama Duduk', 'Peralatan Kantor', 'Pcs'],
            ['P007', 'Pembatas Buku', 'Alat Tulis', 'Pax'],
            ['P008', 'Pita Kain Emas', 'Lain-lain', 'Roll'],
            ['P009', 'Pensil 2B', 'Alat Tulis', 'Pcs'],

            ['R001', 'Rak Susun', 'Peralatan Kantor', 'Pcs'],

            ['S001', 'Sheet Protector', 'Map & Penyimpanan', 'Pcs'],
            ['S002', 'Spidol Warna', 'Alat Tulis', 'Pcs'],
            ['S003', 'Stand Pen STP-Loose', 'Alat Tulis', 'Pcs'],
            ['S004', 'Spectroline 250 Slips', 'Lain-lain', 'Buku'],
            ['S005', 'Spectroline 200 Slips', 'Lain-lain', 'Buku'],
            ['S006', 'Stiker Label', 'Label & Stiker', 'Pcs'],
            ['S007', 'Stiker Bank Papua (Besar Kecil)', 'Label & Stiker', 'Pcs'],
            ['S008', 'Stapler', 'Peralatan Kantor', 'Box'],
            ['S009', 'Slip Jurnal', 'Formulir & Aplikasi', 'Buku'],
            ['S010', 'Spidol Hitam', 'Alat Tulis', 'Pcs'],
            ['S011', 'Sticky Note Kecil', 'Alat Tulis', 'Pcs'],
            ['S012', 'Stabilo', 'Alat Tulis', 'Pcs'],
            ['S013', 'Sticky Note Besar', 'Alat Tulis', 'Pcs'],

            ['T001', 'Tinta Cap', 'Tinta & Pita', 'Botol'],
            ['T002', 'Tinta Epson Kuning 003', 'Tinta & Pita', 'Botol'],
            ['T003', 'Tinta Epson Kuning 664', 'Tinta & Pita', 'Botol'],
            ['T004', 'Tinta Epson Biru 664', 'Tinta & Pita', 'Botol'],
            ['T005', 'Tinta Epson Biru 003', 'Tinta & Pita', 'Botol'],
            ['T006', 'Tinta Epson Hitam 003', 'Tinta & Pita', 'Botol'],
            ['T007', 'Tinta Epson Hitam 664', 'Tinta & Pita', 'Botol'],
            ['T008', 'Tinta Epson Pink 003', 'Tinta & Pita', 'Botol'],
            ['T009', 'Tinta Epson Pink 664', 'Tinta & Pita', 'Botol'],
            ['T010', 'Tipe-X Pita/Roll', 'Alat Tulis', 'Pcs'],
            ['T011A', 'Trigonal Clips Besar', 'Klip & Staples', 'Pcs'],
            ['T011B', 'Trigonal Clips Kecil', 'Klip & Staples', 'Pcs'],
            ['T012', 'Tipe-X Cair', 'Alat Tulis', 'Pcs'],
            ['T013', 'Tinta Kalkulator', 'Tinta & Pita', 'Botol'],
            ['T014', 'Tinta Bantalan Cap', 'Tinta & Pita', 'Botol'],
        ];

        Barang::insert(array_map(fn ($b) => [
            'kode_barang' => $b[0],
            'nama' => $b[1],
            'kategori' => $b[2],
            'satuan' => $b[3],
            'stok' => 0,
            'stok_minimum' => 5,
            'created_at' => $now,
            'updated_at' => $now,
        ], $daftar));
    }
}
