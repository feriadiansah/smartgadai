<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tagihan extends Model
{
     use HasFactory;

    // (Catatan: Kita tidak pakai SoftDeletes di sini karena di file migration
    // Tagihan sebelumnya kita tidak menambahkan $table->softDeletes(). Ini aman.)

    protected $guarded = ['id'];

    /**
     * RELASI KE ATAS (Belongs To)
     * Setiap 1 Tagihan pasti menempel pada 1 Pinjaman/Kontrak.
     */
    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class);
    }
}
