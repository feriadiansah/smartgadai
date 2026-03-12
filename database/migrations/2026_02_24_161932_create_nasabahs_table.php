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
        Schema::create('nasabahs', function (Blueprint $table) {
            $table->id(); // Primary Key

            // Nomor CIF (Customer Information File) / Nomor KTP Bank. Harus unik!
            $table->string('cif')->unique();

            $table->string('nama_lengkap');

            // Nullable (Boleh Kosong) karena di file Excel Lelang terkadang tidak ada nomor HP.
            // Nanti admin bisa melakukan "Update/Edit" manual di Dashboard untuk mengisi nomor HP.
            $table->string('nomor_hp')->nullable();

            $table->timestamps();

            // FITUR BARU: Soft Deletes
            // Mencegah data terhapus permanen dari database jika admin tidak sengaja menekan tombol hapus.
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nasabahs');
    }
};
