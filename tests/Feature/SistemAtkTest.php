<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SistemAtkTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin(): User
    {
        return User::where('username', 'admin')->first();
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
        $this->assertAuthenticatedAs($this->admin());
    }

    public function test_semua_halaman_tampil(): void
    {
        $this->actingAs($this->admin());

        $this->get('/dashboard')->assertOk()
            ->assertSee('Ringkasan Logistik')
            ->assertSee('Kancab Jayapura')
            ->assertSee('Aktivitas Pengeluaran Stok Terbaru');
        $this->get('/stok-barang')->assertOk()
            ->assertSee('Kertas HVS A4 Sinar Dunia 80gr')
            ->assertSee('Hampir Habis');
        $this->get('/stok-barang?q=tinta')->assertOk()
            ->assertSee('Tinta Printer Epson')
            ->assertViewHas('barangs', fn ($b) => $b->total() === 1);
        $this->get('/riwayat-barang')->assertOk()->assertSee('Suplai Vendor');
        $this->get('/riwayat-barang?kategori=Kertas&jenis=masuk')->assertOk()
            ->assertSee('Pengadaan rutin bulanan')
            ->assertViewHas('transaksis', fn ($t) => $t->total() === 1);
    }

    public function test_status_stok(): void
    {
        $this->assertSame(Barang::STATUS_HABIS, new Barang(['stok' => 0, 'stok_minimum' => 20])->status);
        $this->assertSame(Barang::STATUS_HAMPIR_HABIS, new Barang(['stok' => 15, 'stok_minimum' => 20])->status);
        $this->assertSame(Barang::STATUS_TERSEDIA, new Barang(['stok' => 21, 'stok_minimum' => 20])->status);
    }

    public function test_tambah_barang_mencatat_stok_awal(): void
    {
        $this->actingAs($this->admin())->post('/stok-barang', [
            'nama' => 'Lakban Bening', 'kategori' => 'Peralatan Kantor', 'satuan' => 'Pcs',
            'stok' => 30, 'stok_minimum' => 5,
        ])->assertRedirect(route('barang.index'));

        $barang = Barang::where('nama', 'Lakban Bening')->first();
        $this->assertSame(30, $barang->stok);
        $this->assertDatabaseHas('transaksis', ['barang_id' => $barang->id, 'jenis' => 'masuk', 'jumlah' => 30]);
    }

    public function test_barang_keluar_dan_masuk_mengubah_stok(): void
    {
        $barang = Barang::where('nama', 'Stapler Max HD-10')->first(); // stok 28
        $this->actingAs($this->admin());

        $this->post('/transaksi', ['barang_id' => $barang->id, 'jenis' => 'keluar', 'jumlah' => 8, 'oleh' => 'Dewi'])
            ->assertSessionHasNoErrors();
        $this->assertSame(20, $barang->fresh()->stok);

        $this->post('/transaksi', ['barang_id' => $barang->id, 'jenis' => 'masuk', 'jumlah' => 5, 'oleh' => 'Vendor'])
            ->assertSessionHasNoErrors();
        $this->assertSame(25, $barang->fresh()->stok);
    }

    public function test_keluar_tidak_boleh_melebihi_stok(): void
    {
        $barang = Barang::where('nama', 'Spidol Snowman Whiteboard Blue')->first(); // stok 4

        $this->actingAs($this->admin())
            ->post('/transaksi', ['barang_id' => $barang->id, 'jenis' => 'keluar', 'jumlah' => 5, 'oleh' => 'X'])
            ->assertSessionHasErrors('jumlah');
        $this->assertSame(4, $barang->fresh()->stok);
    }

    public function test_permintaan_disetujui_menambah_stok(): void
    {
        $barang = Barang::where('nama', 'Isi Staples Max No. 10')->first(); // stok 0
        $this->actingAs($this->admin());

        $this->post('/permintaan', ['barang_id' => $barang->id, 'jumlah' => 10])->assertSessionHasNoErrors();
        $permintaan = Transaksi::latest('id')->first();
        $this->assertSame('pending', $permintaan->status);
        $this->assertSame(0, $barang->fresh()->stok);

        $this->patch("/permintaan/{$permintaan->id}/setujui")->assertRedirect();
        $this->assertSame(10, $barang->fresh()->stok);
        $this->assertSame('selesai', $permintaan->fresh()->status);

        // Tidak bisa disetujui dua kali.
        $this->patch("/permintaan/{$permintaan->id}/setujui")->assertStatus(422);
        $this->assertSame(10, $barang->fresh()->stok);
    }

    public function test_permintaan_ditolak_tidak_mengubah_stok(): void
    {
        $permintaan = Transaksi::where('status', 'pending')->first();
        $stokAwal = $permintaan->barang->stok;

        $this->actingAs($this->admin())->patch("/permintaan/{$permintaan->id}/tolak")->assertRedirect();
        $this->assertSame('ditolak', $permintaan->fresh()->status);
        $this->assertSame($stokAwal, $permintaan->barang->fresh()->stok);
    }
}
