<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 10);
            $table->foreign('kode_barang')->references('kode_barang')->on('barangs')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('jenis', 20)->index();      // masuk | keluar | koreksi
            $table->integer('jumlah');                 // koreksi bisa negatif (selisih stok)
            $table->string('oleh');                    // nama pelaku / pemohon / vendor
            $table->string('keterangan')->nullable();
            $table->dateTime('tanggal')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
