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
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();

            // FOREIGN KEY: Tagihan ini milik pinjaman/kontrak yang mana?
            $table->foreignId('pinjaman_id')->constrained('pinjamans')->onDelete('cascade');

            // Cicilan bulan ke-berapa? (Untuk Lelang bisa diisi 1 saja atau 0 karena ditagih sekaligus)
            $table->integer('bulan_ke')->default(1);

            // Nominal yang harus dibayar bulan ini (atau total kewajiban saat cut-off)
            $table->decimal('jumlah_tagihan', 15, 2);

            // TANGGAL SANGAT PENTING: Patokan untuk filter "Jatuh Tempo Hari Ini"
            $table->date('tanggal_jatuh_tempo');

            // Status pembayaran: "Belum Lunas", "Lunas", "Jatuh Tempo"
            $table->string('status_pembayaran')->default('Belum Lunas');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
