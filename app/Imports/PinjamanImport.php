<?php

namespace App\Imports;

use App\Models\Nasabah;
use App\Models\Pinjaman;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon; // Pastikan Carbon dipanggil

class PinjamanImport implements ToModel, WithHeadingRow
{
    private $kategori;

    public function __construct($kategori)
    {
        $this->kategori = $kategori;
    }

    public function model(array $row)
    {
        // 1. CEK JUDUL KOLOM (Opsional tapi bagus untuk mencegah error senyap)
        if (!isset($row['cif']) || !isset($row['no_angsuran'])) {
            return null; // Lewati baris jika kosong/salah judul
        }

        // 2. CARI ATAU BUAT NASABAH
        $nasabah = Nasabah::firstOrCreate(
            ['cif' => $row['cif']],
            [
                'nama_lengkap' => $row['nama_nasabah'],
                'nomor_hp'     => $row['no_hp'] ?? null,
            ]
        );

        // Update nomor HP jika sebelumnya kosong
        if (empty($nasabah->nomor_hp) && !empty($row['no_hp'])) {
            $nasabah->update(['nomor_hp' => $row['no_hp']]);
        }

        // 3. LOGIKA TANGGAL PINTAR (SMART DATE PARSER)
        $tanggalExcel = $row['jatuh_tempo'];
        $tanggalJatuhTempo = null;

        if (is_numeric($tanggalExcel)) {
            // Jika admin memakai format Date Excel
            $tanggalJatuhTempo = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalExcel)->format('Y-m-d');
        } else {
            // Jika admin men-copy paste sebagai Text (misal: 2026-04-10 atau 10/04/2026)
            $tanggalJatuhTempo = Carbon::parse($tanggalExcel)->format('Y-m-d');
        }

        // 4. MASUKKAN DATA PINJAMAN (Gunakan no_angsuran)
        return new Pinjaman([
            'nasabah_id'         => $nasabah->id,
            'no_angsuran'          => $row['no_angsuran'], // Kolom DB-mu masih no_kredit kan? Sesuaikan jika sudah diubah jadi no_angsuran
            'produk'             => $row['nama_produk'],
            'sisa_uang_pinjaman' => $row['sisa_pinjaman'],
            'tgl_jatuh_tempo'    => $tanggalJatuhTempo, // Pakai tanggal yang sudah diproses di atas
            'kategori_produk'    => $this->kategori,
            'status_barang'      => 'Aktif'
        ]);
    }
}
