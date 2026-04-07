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
        Schema::create('pinjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nasabah_id')->constrained('nasabahs')->cascadeOnDelete();
            $table->string('no_angsuran');
            $table->string('kategori_produk');
            $table->string('produk')->nullable();

            // PASTIKAN KOLOM INI ADA DAN NAMANYA BENAR
            $table->date('tgl_jatuh_tempo')->nullable();

            $table->decimal('sisa_uang_pinjaman', 15, 2);
            $table->string('status_barang')->default('Menunggu');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjamans');
    }
};
