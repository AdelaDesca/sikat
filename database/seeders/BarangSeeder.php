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
            ['A001', 'Amplop Kop Bank Papua', 'Amplop', 'Box'],
            ['A002', 'Amplop Coklat Kecil', 'Amplop', 'Box'],
            ['A003', 'Amplop Coklat 310', 'Amplop', 'Box'],
            ['A004', 'Amplop Putih 152x90 mm', 'Amplop', 'Box'],
            ['A005', 'Amplop Putih 110x230 mm', 'Amplop', 'Box'],
            ['A006', 'Aplikasi SMS Banking', 'Formulir & Aplikasi', 'Lembar'],
            ['A007', 'Aplikasi Penarikan/Setoran/Pemindahbukuan (1 Lembar)', 'Formulir & Aplikasi', 'Lembar'],
            ['A008', 'Aplikasi Penarikan/Setoran/Pemindahbukuan (2 Lembar)', 'Formulir & Aplikasi', 'Lembar'],
            ['A009', 'Aplikasi Transfer', 'Formulir & Aplikasi', 'Lembar'],

            ['B001', 'Bantex Pocket Bening', 'Map & Penyimpanan', 'Pcs'],
            ['B002', 'Binder Clips No. 155', 'Klip & Staples', 'Box'],
            ['B003', 'Binder Clips DB 105', 'Klip & Staples', 'Box'],
            ['B004', 'Benang Rajut', 'Lain-lain', 'Roll'],
            ['B005', 'Binder Clips DB E-260', 'Klip & Staples', 'Box'],
            ['B006', 'Baterai ABC', 'Peralatan Kantor', 'Pcs'],
            ['B007', 'Bindeks Tempel', 'Label & Stiker', 'Pax'],
            ['B008', 'Buku Tabunganku', 'Buku Tabungan', 'Buku'],
            ['B009', 'Buku Tabungan Simpel', 'Buku Tabungan', 'Buku'],
            ['B010', 'Buku Tabungan Simanja', 'Buku Tabungan', 'Buku'],
            ['B011', 'Buku Tabungan Simpeda', 'Buku Tabungan', 'Buku'],
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

            ['C001', 'Cable Tie', 'Peralatan Kantor', 'Pax'],
            ['C002', 'CD-R', 'Peralatan Kantor', 'Pcs'],
            ['C003', 'Cutter Sedang', 'Alat Tulis', 'Pcs'],
            ['C004', 'Cutter Besar', 'Alat Tulis', 'Pcs'],
            ['C005', 'Cutter Kecil', 'Alat Tulis', 'Pcs'],

            ['D001', 'Display Book', 'Map & Penyimpanan', 'Pcs'],
            ['D002', 'Double Tape Foam', 'Perekat', 'Roll'],
            ['D003', 'Double Tape White', 'Perekat', 'Roll'],
            ['D004', 'DVD-R', 'Peralatan Kantor', 'Pcs'],
            ['D005', 'Telly Doscom (Epson)', 'Lain-lain', 'Pcs'],
            ['D006', 'Double Tape Mounting', 'Perekat', 'Roll'],
            ['D007', 'Double Side Mounting Tape', 'Perekat', 'Roll'],

            ['E001', 'Epson Ribbon Cartridge PLQ-20, 20D', 'Tinta & Pita', 'Pcs'],
            ['E002', 'Epson Ribbon Cartridge LX-310', 'Tinta & Pita', 'Pcs'],

            ['F001', 'Formulir Mobile Banking', 'Formulir & Aplikasi', 'Lembar'],
            ['F002', 'Form Permohonan Pembukaan Rekening', 'Formulir & Aplikasi', 'Lembar'],
            ['F003', 'Form Permohonan Pembukaan Rekening Simpel', 'Formulir & Aplikasi', 'Lembar'],
            ['F004', 'Formulir Pengaduan Nasabah', 'Formulir & Aplikasi', 'Lembar'],
            ['F005', 'Formulir Permohonan Kartu ATM', 'Formulir & Aplikasi', 'Lembar'],
            ['F006', 'Formulir Pendaftaran Merchant', 'Formulir & Aplikasi', 'Lembar'],
            ['F007A', 'Lampiran Form (3 Lembar)', 'Formulir & Aplikasi', 'Lembar'],
            ['F007B', 'Lampiran Form (1 Lembar)', 'Formulir & Aplikasi', 'Lembar'],
            ['F008', 'Form Pengambilan Kartu ATM yang Tertelan', 'Formulir & Aplikasi', 'Lembar'],
            ['F009', 'Form Laporan Kehilangan Kartu ATM Bank Papua', 'Formulir & Aplikasi', 'Lembar'],
            ['F010', 'Form Customer Due Diligence Perorangan', 'Formulir & Aplikasi', 'Lembar'],
            ['F011', 'Form Customer Due Diligence Korporasi', 'Formulir & Aplikasi', 'Lembar'],

            ['G001', 'Glue Stick', 'Perekat', 'Pcs'],

            ['I001', 'Isolasi Bening Kecil', 'Perekat', 'Roll'],
            ['I002', 'Isolasi Coklat Sedang', 'Perekat', 'Roll'],
            ['I003', 'Isi Staples Sedang', 'Klip & Staples', 'Box'],
            ['I004', 'Isi Staples Kecil', 'Klip & Staples', 'Box'],
            ['I005', 'Isi Staples Besar', 'Klip & Staples', 'Box'],
            ['I006', 'Isi Bolpoint', 'Alat Tulis', 'Pcs'],
            ['I007', 'Isolasi Kabel (Unibel)', 'Perekat', 'Roll'],
            ['I008', 'Isi Cutter Besar', 'Alat Tulis', 'Pax'],
            ['I010', 'Isolasi Bening Sedang', 'Perekat', 'Roll'],
            ['I011', 'Isolasi Bening Besar', 'Perekat', 'Roll'],

            ['K001', 'Kertas Karbon Replacement', 'Kertas', 'Pax'],
            ['K002', 'Kertas A4', 'Kertas', 'Rim'],
            ['K003', 'Kertas ATM', 'Kertas', 'Roll'],
            ['K004', 'Kalkulator Casio', 'Peralatan Kantor', 'Unit'],
            ['K005', 'Kertas Kop', 'Kertas', 'Rim'],
            ['K006', 'Kwitansi', 'Formulir & Aplikasi', 'Buku'],
            ['K007', 'Kertas Sertifikat Bank Papua', 'Kertas', 'Lembar'],
            ['K008', 'Kertas Sertifikat Polos', 'Kertas', 'Lembar'],
            ['K009', 'Kwitansi Seri K', 'Formulir & Aplikasi', 'Buku'],
            ['K010', 'Kuas', 'Lain-lain', 'Pcs'],
            ['K011A', 'Sticker Matte Photo Paper', 'Kertas', 'Pax'],
            ['K011B', 'Photo Paper Waterproof Glossy', 'Kertas', 'Pax'],
            ['K011C', 'Transparent Sticker Paper', 'Kertas', 'Pax'],
            ['K011D', 'Silky Photo Paper', 'Kertas', 'Pax'],
            ['K012', 'Kertas Kas', 'Kertas', 'Rim'],
            ['K13', 'Kertas HVS F4', 'Kertas', 'Rim'],
            ['K013', 'Kertas Stiker Photo', 'Kertas', 'Pax'],
            ['K014', 'Kenko Paper Clips No. 5 (Jumbo)', 'Klip & Staples', 'Box'],
            ['K015', 'Kertas Edisi Roll', 'Kertas', 'Roll'],
            ['K016', 'Kartu Stock', 'Kertas', 'Lembar'],
            ['K017', 'Kertas Termal Nomor Antrian', 'Kertas', 'Roll'],

            ['L001', 'Lakban Bening Besar', 'Perekat', 'Roll'],
            ['L002', 'Label Uang Rp 100.000', 'Label & Stiker', 'Pax'],
            ['L003', 'Lakban Hitam', 'Perekat', 'Roll'],
            ['L004', 'Label Uang Rp 50.000', 'Label & Stiker', 'Pax'],
            ['L005', 'Label Uang Rp 20.000', 'Label & Stiker', 'Pax'],
            ['L006', 'Label Uang Rp 10.000', 'Label & Stiker', 'Pax'],
            ['L007', 'Label Uang Rp 5.000', 'Label & Stiker', 'Pax'],
            ['L008', 'Label Uang Rp 2.000', 'Label & Stiker', 'Pax'],
            ['L009', 'Label Uang Rp 1.000', 'Label & Stiker', 'Pax'],
            ['L010', 'Lakban Coklat Besar', 'Perekat', 'Roll'],
            ['L011', 'Lakban Kertas Besar', 'Perekat', 'Roll'],
            ['L012', 'Lakban Kertas', 'Perekat', 'Roll'],
            ['L013', 'Lakban Bening Sedang', 'Perekat', 'Roll'],
            ['L014', 'Lakban Coklat Sedang', 'Perekat', 'Roll'],

            ['M001', 'Map', 'Map & Penyimpanan', 'Pcs'],
            ['M002', 'Minyak Mesin', 'Lain-lain', 'Botol'],
            ['M003', 'Map Album', 'Map & Penyimpanan', 'Pcs'],
            ['M004', 'Money Detector', 'Peralatan Kantor', 'Unit'],
            ['M005A', 'Map Batik', 'Map & Penyimpanan', 'Pcs'],
            ['M005B', 'Mousepad', 'Peralatan Kantor', 'Pcs'],

            ['N001', 'Nota', 'Formulir & Aplikasi', 'Buku'],
            ['O001', 'Ordner Folio', 'Map & Penyimpanan', 'Pcs'],

            ['P001', 'Plastik Bening', 'Lain-lain', 'Pax'],
            ['P002', 'Plastik Kresek Hitam', 'Lain-lain', 'Pax'],
            ['P003', 'Povinal Liquid', 'Perekat', 'Botol'],
            ['P004', 'Penghapus', 'Alat Tulis', 'Pcs'],
            ['P005', 'Penggaris', 'Alat Tulis', 'Pcs'],
            ['P006', 'Papan Nama Duduk', 'Peralatan Kantor', 'Unit'],
            ['P007', 'Pembatas Buku', 'Alat Tulis', 'Pcs'],
            ['P008', 'Pita Kain Emas', 'Lain-lain', 'Roll'],
            ['P009', 'Pensil 2B', 'Alat Tulis', 'Pcs'],

            ['R001', 'Rak Susun', 'Peralatan Kantor', 'Unit'],

            ['S001', 'Sheet Protector', 'Map & Penyimpanan', 'Pax'],
            ['S002', 'Spidol Warna', 'Alat Tulis', 'Pcs'],
            ['S003', 'Stand Pen STP-Loose', 'Alat Tulis', 'Pcs'],
            ['S004', 'Spectroline 250 Slips', 'Lain-lain', 'Pcs'],
            ['S005', 'Spectroline 200 Slips', 'Lain-lain', 'Pcs'],
            ['S006', 'Stiker Label', 'Label & Stiker', 'Pax'],
            ['S007', 'Stiker Bank Papua (Besar Kecil)', 'Label & Stiker', 'Lembar'],
            ['S008', 'Stapler', 'Peralatan Kantor', 'Unit'],
            ['S009', 'Slip Jurnal', 'Formulir & Aplikasi', 'Buku'],
            ['S010', 'Spidol Hitam', 'Alat Tulis', 'Pcs'],
            ['S011', 'Sticky Note Kecil', 'Alat Tulis', 'Pax'],
            ['S012', 'Stabilo', 'Alat Tulis', 'Pcs'],
            ['S013', 'Sticky Note Besar', 'Alat Tulis', 'Pax'],

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
            ['T011A', 'Trigonal Clips Besar', 'Klip & Staples', 'Box'],
            ['T011B', 'Trigonal Clips Kecil', 'Klip & Staples', 'Box'],
            ['T012', 'Tipe-X Cair', 'Alat Tulis', 'Pcs'],
            ['T013', 'Tinta Kalkulator', 'Tinta & Pita', 'Pcs'],
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
