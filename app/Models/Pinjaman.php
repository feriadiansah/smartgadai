<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pinjaman extends Model
{
    use HasFactory, SoftDeletes;

    // BARIS WAJIB: Memaksa Laravel memakai tabel 'pinjamans' (bukan 'pinjamen')
    protected $table = 'pinjamans';

    // Pakai guarded untuk mempermudah Import Excel
    protected $guarded = ['id'];

    /**
     * RELASI KE ATAS (Belongs To)
     * Setiap 1 Pinjaman/Kontrak PASTI milik 1 Nasabah.
     * Nanti di tampilan web, kita bisa panggil: $pinjaman->nasabah->nama_lengkap
     */
    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class);
    }

    /**
     * RELASI KE BAWAH (Has Many)
     * Setiap 1 Pinjaman/Kontrak, bisa memiliki banyak Tagihan Bulanan.
     */
    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }
}
