<?php

namespace Database\Seeders;

use App\Models\Barang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * DATA DUMMY transaksi historis Januari–September 2026 untuk bahan analisis & visualisasi.
 *
 * - Hanya menulis ke tabel transaksis; stok barang TIDAK diubah (stok sekarang = hasil hitung fisik).
 * - Hanya mengisi rentang sebelum BATAS (data asli sistem dimulai 1 Oktober 2026).
 * - Aman dijalankan ulang: transaksi sebelum BATAS dihapus dulu lalu dibuat ulang (hasil selalu sama).
 *
 * Jalankan : php artisan db:seed --class=TransaksiHistorisSeeder
 * Hapus    : DB::table('transaksis')->where('tanggal', '<', '2026-10-01')->delete();
 */
class TransaksiHistorisSeeder extends Seeder
{
    public const BATAS = '2026-10-01';

    private const MULAI = '2026-01-01';

    /** Departemen kantor cabang (ditulis singkat di kolom keterangan). */
    private const DEPT_LAYANAN = 'Dept.Layanan';
    private const DEPT_PO = 'Dept.PO';
    private const DEPT_PKDJ = 'Dept.PKDJ';
    private const PEMIMPIN_CABANG = 'Pak Susanto';

    /** Pegawai Dept.PO: Kak Jilly paling sibuk, pemimpin departemen jarang meminta. */
    private const PEGAWAI_PO = ['Kak Jilly' => 12, 'Kak Middel' => 5, 'Mba Mala' => 5, 'Pak Prast' => 5, 'Ibu Uli' => 1];

    /** Pegawai Dept.PKDJ: Kak Ririn, Pak Fadel & Pak Agung paling sibuk, pemimpin departemen jarang meminta. */
    private const PEGAWAI_PKDJ = ['Kak Ririn' => 10, 'Pak Fadel' => 10, 'Pak Agung' => 10, 'Pak Sutrisno' => 3, 'Pak Faqih' => 3, 'Ibu Ria' => 1];

    /**
     * Kelompok peminta per jenis kebutuhan: [departemen, [nama pegawai => bobot]].
     * Bobot lebih besar = lebih sering meminta ATK.
     */
    private const UNIT = [
        'Teller' => [self::DEPT_LAYANAN, ['Kak Anaci' => 6, 'Ibu Nur' => 1]],
        'Customer Service' => [self::DEPT_LAYANAN, ['Kak Nita' => 6, 'Kak Yuda' => 6, 'Ibu Nur' => 1]],
        'Operasional' => [self::DEPT_PO, self::PEGAWAI_PO],
        'Akuntansi' => [self::DEPT_PO, self::PEGAWAI_PO],
        'Umum' => [self::DEPT_PO, self::PEGAWAI_PO],
        'Kredit' => [self::DEPT_PKDJ, self::PEGAWAI_PKDJ],
        'Pemasaran' => [self::DEPT_PKDJ, self::PEGAWAI_PKDJ],
    ];

    /** Barang yang sesekali diminta Pemimpin Cabang: kode => [min, maks]. */
    private const PERMINTAAN_PEMIMPIN_CABANG = [
        'A001' => [5, 10], 'B020' => [1, 2], 'S011' => [1, 2], 'M001' => [2, 5],
        'D001' => [1, 1], 'K002' => [1, 1], 'O001' => [1, 1], 'T006' => [1, 1],
    ];

    /**
     * Jam pengambilan ATK (jam kerja 08.00–17.00); jam ramai pagi & siang lebih sering muncul,
     * jam istirahat (12.00) dan menjelang pulang lebih jarang.
     */
    private const JAM_KERJA = [8, 8, 9, 9, 9, 10, 10, 10, 11, 11, 12, 13, 13, 14, 14, 14, 15, 15, 16];

    /** Hari libur nasional / cuti bersama 2026 (kantor tutup). */
    private const LIBUR = [
        '2026-01-01', '2026-01-16', '2026-02-17', '2026-03-18', '2026-03-19', '2026-03-20',
        '2026-03-23', '2026-03-24', '2026-04-03', '2026-05-01', '2026-05-14', '2026-05-27',
        '2026-05-31', '2026-06-01', '2026-06-16', '2026-08-17', '2026-08-25',
    ];

    /** Faktor musiman umum per bulan (Jan..Sep): awal tahun & tutup semester lebih ramai. */
    private const MUSIM = [1 => 1.15, 1.0, 1.1, 0.9, 1.0, 1.1, 1.1, 0.95, 1.05];

    /**
     * Profil pemakaian per barang:
     * kode => [rata-rata keluar per bulan, min per ambil, maks per ambil, unit peminta, kelompok musiman]
     * Barang yang tidak tercantum dianggap jarang/tidak pernah dipakai (stok mati).
     */
    private const PROFIL = [
        // Kertas
        'K002' => [18, 1, 3, 'semua'],
        'K13' => [6, 1, 2, ['Kredit', 'Akuntansi', 'Umum']],
        'K003' => [8, 2, 4, ['Operasional']],
        'K017' => [12, 2, 4, ['Customer Service', 'Teller']],
        'K005' => [2, 1, 1, ['Umum']],
        'K012' => [2, 1, 1, ['Teller', 'Akuntansi']],
        'K007' => [20, 5, 10, ['Pemasaran']],
        'K016' => [10, 5, 10, ['Umum']],
        'K011B' => [10, 5, 10, ['Pemasaran']],
        'K013' => [10, 5, 10, ['Pemasaran']],
        // Alat tulis
        'B020' => [40, 5, 12, 'semua'],
        'B017' => [15, 3, 6, ['Teller', 'Customer Service']],
        'I006' => [10, 2, 5, 'semua'],
        'S011' => [10, 2, 4, 'semua'],
        'S013' => [5, 1, 2, 'semua'],
        'S012' => [6, 1, 3, 'semua'],
        'S010' => [4, 1, 2, ['Umum', 'Pemasaran', 'Kredit']],
        'S002' => [2, 1, 2, ['Pemasaran']],
        'T012' => [3, 1, 1, 'semua'],
        'T010' => [3, 1, 1, 'semua'],
        'P004' => [2, 1, 1, 'semua'],
        'P009' => [3, 1, 2, 'semua'],
        'P005' => [0.5, 1, 1, 'semua'],
        'B015' => [2, 1, 1, ['Akuntansi', 'Umum']],
        'B016' => [2, 1, 1, ['Akuntansi', 'Umum']],
        'C003' => [0.5, 1, 1, ['Umum']],
        'I008' => [0.5, 1, 1, ['Umum']],
        // Formulir & aplikasi (lembar)
        // Slip penarikan/setoran/transfer: CS mengisi rak slip di banking hall, Teller sesekali menyetok mejanya.
        'A007' => [12, 2, 4, ['Customer Service', 'Customer Service', 'Customer Service', 'Teller']],
        'A008' => [6, 1, 3, ['Customer Service', 'Customer Service', 'Customer Service', 'Teller']],
        'A009' => [4, 1, 2, ['Customer Service', 'Customer Service', 'Customer Service', 'Teller']],
        'A006' => [2, 1, 1, ['Customer Service']],
        'F001' => [2, 1, 1, ['Customer Service']],
        'F002' => [3, 1, 2, ['Customer Service'], 'tabungan'],
        'F003' => [1, 1, 1, ['Customer Service'], 'tabungan'],
        'F004' => [0.5, 1, 1, ['Customer Service']],
        'F005' => [2, 1, 1, ['Customer Service']],
        'F007A' => [1, 1, 1, ['Customer Service']],
        'F007B' => [0.7, 1, 1, ['Customer Service']],
        'F008' => [0.2, 1, 1, ['Customer Service']],
        'F009' => [0.3, 1, 1, ['Customer Service']],
        'F010' => [3, 1, 2, ['Customer Service']],
        'F011' => [0.4, 1, 1, ['Customer Service', 'Kredit']],
        'B012' => [4, 1, 2, ['Akuntansi', 'Teller']],
        'S009' => [6, 1, 2, ['Akuntansi', 'Operasional']],
        'K006' => [3, 1, 1, ['Umum', 'Akuntansi']],
        'N001' => [1, 1, 1, ['Umum']],
        // Buku tabungan
        'B008' => [2, 1, 1, ['Customer Service'], 'tabungan'],
        'B009' => [1.5, 1, 1, ['Customer Service'], 'tabungan'],
        'B010' => [1.5, 1, 1, ['Customer Service'], 'tabungan'],
        'B011' => [5, 1, 2, ['Customer Service'], 'tabungan'],
        // Amplop
        'A001' => [150, 20, 50, ['Umum', 'Pemasaran', 'Kredit'], 'amplop'],
        'A004' => [100, 20, 40, ['Umum', 'Kredit'], 'amplop'],
        'A005' => [80, 20, 30, ['Umum', 'Pemasaran'], 'amplop'],
        'A002' => [40, 10, 20, ['Umum'], 'amplop'],
        'A003' => [30, 10, 15, ['Kredit', 'Umum'], 'amplop'],
        // Label uang (melonjak menjelang Lebaran)
        'L002' => [10, 2, 4, ['Teller'], 'label'],
        'L004' => [8, 2, 3, ['Teller'], 'label'],
        'L005' => [4, 1, 2, ['Teller'], 'label'],
        'L006' => [3, 1, 2, ['Teller'], 'label'],
        'L007' => [2, 1, 1, ['Teller'], 'label'],
        'L008' => [2, 1, 1, ['Teller'], 'label'],
        'L009' => [1, 1, 1, ['Teller'], 'label'],
        // Tinta & pita
        'E001' => [3, 1, 1, ['Teller', 'Customer Service']],
        'E002' => [2, 1, 1, ['Akuntansi']],
        'T006' => [4, 1, 2, 'semua'],
        'T002' => [1, 1, 1, 'semua'],
        'T005' => [1, 1, 1, 'semua'],
        'T008' => [1, 1, 1, 'semua'],
        'T007' => [2, 1, 1, ['Kredit', 'Akuntansi']],
        'T003' => [0.7, 1, 1, ['Kredit', 'Akuntansi']],
        'T004' => [0.7, 1, 1, ['Kredit', 'Akuntansi']],
        'T009' => [0.7, 1, 1, ['Kredit', 'Akuntansi']],
        'T001' => [1, 1, 1, ['Teller', 'Customer Service']],
        'T014' => [1, 1, 1, ['Teller', 'Customer Service']],
        'T013' => [0.5, 1, 1, ['Teller']],
        // Klip & staples
        'I004' => [8, 1, 3, 'semua'],
        'I003' => [4, 1, 2, 'semua'],
        'B003' => [2, 1, 1, 'semua'],
        'B013' => [1.5, 1, 1, 'semua'],
        'B014' => [1, 1, 1, 'semua'],
        'B002' => [1, 1, 1, 'semua'],
        'B021' => [0.5, 1, 1, ['Kredit', 'Akuntansi']],
        'B022' => [0.5, 1, 1, ['Kredit', 'Akuntansi']],
        'K014' => [1.5, 1, 1, 'semua'],
        // Perekat
        'L001' => [3, 1, 1, ['Umum', 'Operasional']],
        'L010' => [2, 1, 1, ['Umum', 'Operasional']],
        'L003' => [0.5, 1, 1, ['Umum']],
        'L012' => [1, 1, 1, ['Umum']],
        'L013' => [1, 1, 1, ['Umum']],
        'I001' => [4, 1, 2, 'semua'],
        'I010' => [2, 1, 1, 'semua'],
        'I011' => [1, 1, 1, ['Umum']],
        'I002' => [1, 1, 1, ['Umum']],
        'D002' => [1, 1, 1, ['Umum', 'Pemasaran']],
        'D003' => [1, 1, 1, ['Umum', 'Pemasaran']],
        'G001' => [3, 1, 1, 'semua'],
        // Map & penyimpanan
        'M001' => [40, 10, 20, ['Kredit', 'Customer Service', 'Umum']],
        'O001' => [4, 1, 2, ['Akuntansi', 'Kredit', 'Umum']],
        'B018' => [2, 1, 1, ['Akuntansi', 'Umum']],
        'B001' => [10, 5, 5, ['Kredit', 'Customer Service']],
        'S001' => [20, 5, 10, ['Kredit', 'Akuntansi']],
        'D001' => [2, 1, 1, ['Pemasaran', 'Kredit']],
        'B023' => [6, 2, 3, ['Kredit']],
        // Label, stiker & lain-lain
        'S006' => [1, 1, 1, ['Umum']],
        'S007' => [10, 2, 5, ['Pemasaran']],
        'B007' => [2, 1, 1, ['Akuntansi', 'Umum']],
        'P001' => [10, 5, 5, ['Umum']],
        'P002' => [10, 5, 5, ['Umum']],
        'B006' => [4, 2, 2, ['Umum']],
        'C001A' => [0.4, 1, 1, ['Umum', 'Operasional']],
        'C001B' => [0.5, 1, 1, ['Umum', 'Operasional']],
        'K004' => [0.15, 1, 1, ['Teller']],
        'S008' => [0.3, 1, 1, 'semua'],
    ];

    public function run(): void
    {
        mt_srand(20260101); // hasil acak selalu sama setiap dijalankan

        $kodeAda = Barang::pluck('kode_barang')->flip();
        $hariKerja = $this->hariKerjaPerBulan();
        $rows = [];

        foreach (self::PROFIL as $kode => $p) {
            if (! isset($kodeAda[$kode])) {
                $this->command?->warn("Kode {$kode} tidak ada di tabel barangs, dilewati.");
                continue;
            }

            [$rata, $min, $maks, $unit] = $p;
            $kelompok = $p[4] ?? null;
            $daftarUnit = $unit === 'semua' ? array_keys(self::UNIT) : $unit;

            foreach ($hariKerja as $bulan => $hari) {
                // Barang keluar: kebutuhan bulan ini dipecah menjadi beberapa pengambilan.
                $kebutuhan = $rata * self::MUSIM[$bulan] * $this->faktorKhusus($kelompok, $bulan) * $this->acak(0.75, 1.25);
                $sisa = (int) floor($kebutuhan) + ($this->acak(0, 1) < fmod($kebutuhan, 1) ? 1 : 0);
                $keluarBulanIni = $sisa;

                while ($sisa > 0) {
                    $jumlah = min($sisa, mt_rand($min, $maks));
                    $sisa -= $jumlah;
                    [$dept, $pegawai] = self::UNIT[$daftarUnit[array_rand($daftarUnit)]];
                    $rows[] = $this->baris($kode, 'keluar', $jumlah, $this->pilihBerbobot($pegawai),
                        $dept, $this->waktuAcak($hari[array_rand($hari)]));
                }

                // Barang masuk: pengadaan bulanan untuk barang yang sering dipakai, triwulanan untuk sisanya.
                if ($rata >= 3) {
                    $masuk = (int) (ceil($rata * $this->acak(1.0, 1.2) / $maks) * $maks);
                    $rows[] = $this->baris($kode, 'masuk', $masuk, 'Admin', 'Pengadaan rutin bulanan',
                        $this->waktuAcak($hari[0], 8, 9));
                } elseif (in_array($bulan, [1, 4, 7], true) && ($rata >= 0.3 || $keluarBulanIni > 0)) {
                    $masuk = max(1, (int) ceil($rata * 3 * $this->acak(1.0, 1.3)));
                    $rows[] = $this->baris($kode, 'masuk', $masuk, 'Admin', 'Pengadaan triwulan',
                        $this->waktuAcak($hari[0], 8, 9));
                }
            }
        }

        // Pemimpin Cabang hanya sesekali meminta ATK (0–2 kali per bulan).
        foreach ($hariKerja as $hari) {
            for ($i = mt_rand(0, 2); $i > 0; $i--) {
                $kode = array_rand(self::PERMINTAAN_PEMIMPIN_CABANG);
                [$min, $maks] = self::PERMINTAAN_PEMIMPIN_CABANG[$kode];
                $rows[] = $this->baris($kode, 'keluar', mt_rand($min, $maks), self::PEMIMPIN_CABANG,
                    'Pemimpin Cabang', $this->waktuAcak($hari[array_rand($hari)]));
            }
        }

        usort($rows, fn ($a, $b) => strcmp($a['tanggal'], $b['tanggal']));

        DB::transaction(function () use ($rows) {
            DB::table('transaksis')->where('tanggal', '<', self::BATAS)->delete();
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('transaksis')->insert($chunk);
            }
        });

        $this->command?->info(count($rows).' transaksi historis dibuat ('.self::MULAI.' s/d sebelum '.self::BATAS.'). Stok barang tidak diubah.');
    }

    /** Lonjakan musiman khusus: label uang & amplop menjelang Lebaran (Maret), tabungan saat tahun ajaran baru (Juli). */
    private function faktorKhusus(?string $kelompok, int $bulan): float
    {
        return match (true) {
            $kelompok === 'label' && $bulan === 3 => 1.9,
            $kelompok === 'label' && $bulan === 2 => 1.3,
            $kelompok === 'amplop' && $bulan === 3 => 1.5,
            $kelompok === 'tabungan' && $bulan === 7 => 1.6,
            $kelompok === 'tabungan' && $bulan === 1 => 1.2,
            default => 1.0,
        };
    }

    /** @return array<int, array<int, Carbon>> hari kerja (Senin–Jumat, bukan libur) per bulan */
    private function hariKerjaPerBulan(): array
    {
        $hasil = [];
        $tgl = Carbon::parse(self::MULAI);
        $akhir = Carbon::parse(self::BATAS);

        while ($tgl->lt($akhir)) {
            if ($tgl->isWeekday() && ! in_array($tgl->toDateString(), self::LIBUR, true)) {
                $hasil[$tgl->month][] = $tgl->copy();
            }
            $tgl->addDay();
        }

        return $hasil;
    }

    /** Waktu acak di jam kerja (08.00–16.59), atau di rentang jam tertentu bila diberikan. */
    private function waktuAcak(Carbon $hari, ?int $jamMulai = null, ?int $jamAkhir = null): string
    {
        $jam = $jamMulai === null
            ? self::JAM_KERJA[array_rand(self::JAM_KERJA)]
            : mt_rand($jamMulai, $jamAkhir);

        return $hari->copy()->setTime($jam, mt_rand(0, 59))->toDateTimeString();
    }

    /** @param array<string, int> $pilihan nama => bobot */
    private function pilihBerbobot(array $pilihan): string
    {
        $titik = mt_rand(1, array_sum($pilihan));
        foreach ($pilihan as $nama => $bobot) {
            if (($titik -= $bobot) <= 0) {
                return $nama;
            }
        }

        return array_key_last($pilihan);
    }

    private function acak(float $min, float $maks): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($maks - $min);
    }

    /** @return array<string, mixed> */
    private function baris(string $kode, string $jenis, int $jumlah, string $oleh, string $keterangan, string $tanggal): array
    {
        return compact('jenis', 'jumlah', 'oleh', 'keterangan', 'tanggal') + [
            'kode_barang' => $kode,
            'created_at' => $tanggal,
            'updated_at' => $tanggal,
        ];
    }
}
