<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemAtkTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create(['username' => 'admin', 'password' => 'admin123']);
    }

    private function barang(string $kode, string $nama, int $stok, string $kategori = 'Alat Tulis'): Barang
    {
        return Barang::create([
            'kode_barang' => $kode, 'nama' => $nama, 'kategori' => $kategori, 'stok' => $stok, 'satuan' => 'Pcs', 'stok_minimum' => 5,
        ]);
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Sistem Kelola ATK');
    }

    public function test_login_dengan_username(): void
    {
        $this->post('/login', ['username' => 'admin', 'password' => 'salah'])
            ->assertSessionHasErrors('username');

        $this->post('/login', ['username' => 'admin', 'password' => 'admin123', 'remember' => '1'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_semua_halaman_tampil(): void
    {
        $tinta = $this->barang('T006', 'Tinta Epson Hitam 003', 3, 'Tinta & Pita');
        $kertas = $this->barang('K002', 'Kertas A4', 40, 'Kertas');
        $kertas->transaksis()->create([
            'jenis' => 'masuk', 'jumlah' => 40, 'oleh' => 'Vendor', 'keterangan' => 'Pengadaan rutin', 'tanggal' => now(),
        ]);
        $tinta->transaksis()->create([
            'jenis' => 'keluar', 'jumlah' => 2, 'oleh' => 'Teller', 'keterangan' => 'Printer teller', 'tanggal' => now(),
        ]);

        $this->actingAs($this->admin);

        $this->get('/dashboard')->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Admin')
            ->assertSee('Aktivitas Pengeluaran Stok Terbaru');
        $this->get('/stok-barang')->assertOk()
            ->assertSee('Kertas A4')
            ->assertSee('Hampir Habis')
            ->assertDontSee('Minta ATK');
        $this->get('/stok-barang?q=tinta')->assertOk()
            ->assertSee('Tinta Epson Hitam 003')
            ->assertViewHas('barangs', fn ($b) => $b->total() === 1);
        $this->get('/riwayat-barang')->assertOk()->assertSee('Printer teller');
        $this->get('/riwayat-barang?q=teller')->assertOk()
            ->assertSee('Tinta Epson Hitam 003')
            ->assertViewHas('transaksis', fn ($t) => $t->total() === 1);
        $this->get('/riwayat-barang?q=K002')->assertOk()
            ->assertSee('Pengadaan rutin')
            ->assertViewHas('transaksis', fn ($t) => $t->total() === 1);
        $this->get('/riwayat-barang?kategori=Kertas&jenis=masuk')->assertOk()
            ->assertSee('Pengadaan rutin')
            ->assertViewHas('transaksis', fn ($t) => $t->total() === 1);
    }

    public function test_report_kesehatan_stok(): void
    {
        $this->barang('K002', 'Kertas A4', 40, 'Kertas');         // tersedia
        $this->barang('T006', 'Tinta Epson Hitam 003', 3, 'Tinta & Pita'); // hampir habis (batas 5)
        $this->barang('T001', 'Tinta Cap', 0, 'Tinta & Pita');     // habis
        $this->actingAs($this->admin);

        $this->get('/report')->assertOk()
            ->assertSee('Kesehatan Stok')
            ->assertSee('67%') // 2 dari 3 barang perlu diisi ulang
            ->assertViewHas('ringkasan', fn ($r) => $r->all() === ['habis' => 1, 'hampir_habis' => 1, 'tersedia' => 1])
            ->assertViewHas('kritis', fn ($k) => $k->pluck('kode_barang')->all() === ['T001', 'T006']);

        $this->get('/report?kategori=Kertas')->assertOk()
            ->assertViewHas('total', 1)
            ->assertViewHas('kritis', fn ($k) => $k->isEmpty())
            ->assertSee('Tidak ada barang kritis');
    }

    public function test_status_stok(): void
    {
        $this->assertSame(Barang::STATUS_HABIS, new Barang(['stok' => 0, 'stok_minimum' => 20])->status);
        $this->assertSame(Barang::STATUS_HAMPIR_HABIS, new Barang(['stok' => 15, 'stok_minimum' => 20])->status);
        $this->assertSame(Barang::STATUS_TERSEDIA, new Barang(['stok' => 21, 'stok_minimum' => 20])->status);
    }

    public function test_tambah_barang_mencatat_stok_awal(): void
    {
        $this->actingAs($this->admin)->post('/stok-barang', [
            'kode_barang' => 'l015', 'nama' => 'Lakban Bening', 'kategori' => 'Perekat', 'satuan' => 'Roll',
            'stok' => 30, 'stok_minimum' => 5,
        ])->assertRedirect(route('barang.index'));

        $barang = Barang::find('L015'); // kode otomatis jadi huruf besar
        $this->assertSame(30, $barang->stok);
        $this->assertDatabaseHas('transaksis', [
            'kode_barang' => $barang->kode_barang, 'jenis' => 'masuk', 'jumlah' => 30, 'oleh' => 'Admin',
        ]);
    }

    public function test_barang_keluar_dan_masuk_mengubah_stok(): void
    {
        $barang = $this->barang('S008', 'Stapler', 28);
        $this->actingAs($this->admin);

        $this->post('/transaksi', ['kode_barang' => $barang->kode_barang, 'jenis' => 'keluar', 'jumlah' => 8, 'oleh' => 'Dewi'])
            ->assertSessionHasNoErrors();
        $this->assertSame(20, $barang->fresh()->stok);

        $this->post('/transaksi', ['kode_barang' => $barang->kode_barang, 'jenis' => 'masuk', 'jumlah' => 5, 'oleh' => 'Vendor'])
            ->assertSessionHasNoErrors();
        $this->assertSame(25, $barang->fresh()->stok);
    }

    public function test_transaksi_bisa_dicatat_dengan_tanggal_lampau(): void
    {
        $barang = $this->barang('K002', 'Kertas A4', 10);
        $this->actingAs($this->admin);
        $kemarin = now()->subDay()->setTime(15, 30);

        $this->post('/transaksi', [
            'kode_barang' => 'K002', 'jenis' => 'keluar', 'jumlah' => 2, 'oleh' => 'Dian',
            'tanggal' => $kemarin->format('Y-m-d\TH:i'),
        ])->assertSessionHasNoErrors();

        $this->assertSame(8, $barang->fresh()->stok);
        $this->assertDatabaseHas('transaksis', ['kode_barang' => 'K002', 'tanggal' => $kemarin->format('Y-m-d H:i:s')]);

        // Tanggal di masa depan ditolak.
        $this->post('/transaksi', [
            'kode_barang' => 'K002', 'jenis' => 'masuk', 'jumlah' => 5, 'oleh' => 'Admin',
            'tanggal' => now()->addDay()->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors('tanggal');
        $this->assertSame(8, $barang->fresh()->stok);
    }

    public function test_keluar_tidak_boleh_melebihi_stok(): void
    {
        $barang = $this->barang('S010', 'Spidol Hitam', 4);

        $this->actingAs($this->admin)
            ->post('/transaksi', ['kode_barang' => $barang->kode_barang, 'jenis' => 'keluar', 'jumlah' => 5, 'oleh' => 'X'])
            ->assertSessionHasErrors('jumlah');
        $this->assertSame(4, $barang->fresh()->stok);
    }

    public function test_seeder_memuat_atk_asli(): void
    {
        $this->seed(\Database\Seeders\BarangSeeder::class);

        $this->assertSame(152, Barang::count());
        $this->assertSame(0, (int) Barang::sum('stok'));
        $this->assertDatabaseHas('barangs', [
            'kode_barang' => 'B011', 'nama' => 'Buku Tabungan Simpeda', 'kategori' => 'Buku Tabungan',
        ]);
        $this->assertDatabaseHas('barangs', ['kode_barang' => 'F007B', 'nama' => 'Lampiran Form (1 Lembar)']);
    }

    public function test_data_historis_tidak_mengubah_stok_dan_data_asli(): void
    {
        $this->seed(\Database\Seeders\BarangSeeder::class);
        Barang::where('kode_barang', 'K002')->update(['stok' => 40]);
        $asli = Barang::find('K002')->transaksis()->create([
            'jenis' => 'keluar', 'jumlah' => 1, 'oleh' => 'Admin', 'tanggal' => '2026-10-02 09:00:00',
        ]);
        $stokSebelum = (int) Barang::sum('stok');

        $this->seed(\Database\Seeders\TransaksiHistorisSeeder::class);

        $this->assertSame($stokSebelum, (int) Barang::sum('stok'));
        $this->assertNotNull($asli->fresh());
        $this->assertGreaterThan(1000, \App\Models\Transaksi::where('tanggal', '<', '2026-10-01')->count());
        $this->assertSame(1, \App\Models\Transaksi::where('tanggal', '>=', '2026-10-01')->count());
    }

    public function test_kode_barang_tidak_boleh_ganda(): void
    {
        $this->barang('A001', 'Amplop Kop Bank Papua', 0);

        $this->actingAs($this->admin)->post('/stok-barang', [
            'kode_barang' => 'A001', 'nama' => 'Amplop Lain', 'kategori' => 'Amplop', 'satuan' => 'Box',
            'stok' => 0, 'stok_minimum' => 5,
        ])->assertSessionHasErrors('kode_barang');

        $this->assertSame(1, Barang::count());
    }

    public function test_ubah_kode_barang_ikut_mengubah_riwayat(): void
    {
        $barang = $this->barang('X001', 'Gunting', 3);
        $barang->transaksis()->create([
            'jenis' => 'keluar', 'jumlah' => 1, 'oleh' => 'Teller', 'tanggal' => now(),
        ]);

        $this->actingAs($this->admin)->put('/stok-barang/X001', [
            'kode_barang' => 'G002', 'nama' => 'Gunting', 'kategori' => 'Alat Tulis', 'satuan' => 'Pcs',
            'stok' => 3, 'stok_minimum' => 5,
        ])->assertSessionHasNoErrors();

        $this->assertNull(Barang::find('X001'));
        $this->assertSame(1, Barang::find('G002')->transaksis()->count());
    }

    public function test_koreksi_stok_lewat_edit_tidak_dihitung_barang_keluar(): void
    {
        $barang = $this->barang('A001', 'Amplop Kop Bank Papua', 150);
        $this->actingAs($this->admin);

        $this->put('/stok-barang/A001', [
            'kode_barang' => 'A001', 'nama' => 'Amplop Kop Bank Papua', 'kategori' => 'Amplop', 'satuan' => 'Box',
            'stok' => 50, 'stok_minimum' => 5,
        ])->assertSessionHasNoErrors();

        $this->assertSame(50, $barang->fresh()->stok);
        $this->assertDatabaseHas('transaksis', ['kode_barang' => 'A001', 'jenis' => 'koreksi', 'jumlah' => -100]);
        $this->assertDatabaseMissing('transaksis', ['kode_barang' => 'A001', 'jenis' => 'keluar']);

        $this->get('/riwayat-barang')->assertSee('Koreksi stok 150 → 50')->assertSee('-100');

        // Edit tanpa mengubah stok tidak membuat catatan koreksi.
        $this->put('/stok-barang/A001', [
            'kode_barang' => 'A001', 'nama' => 'Amplop Kop', 'kategori' => 'Amplop', 'satuan' => 'Box',
            'stok' => 50, 'stok_minimum' => 10,
        ])->assertSessionHasNoErrors();
        $this->assertSame(1, $barang->transaksis()->count());
    }
}
