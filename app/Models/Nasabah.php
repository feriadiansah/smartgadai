<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nasabah extends Model
{
    use HasFactory, SoftDeletes;

    // Pakai guarded agar aman saat import Excel massal (semua kolom boleh diisi otomatis kecuali ID)
    protected $guarded = ['id'];

    /**
     * RELASI DATABASE (One-to-Many)
     * Satu Nasabah bisa punya banyak Pinjaman/Kontrak (Misal: 1 utang motor, 1 utang emas).
     * Fungsi ini akan sangat berguna saat memanggil data di Dashboard nanti.
     */
    public function pinjamans(): HasMany
    {
        return $this->hasMany(Pinjaman::class);
    }
}
