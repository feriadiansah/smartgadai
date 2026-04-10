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
        // 1. CEK JUDUL KOLOM (Cegah error kalau baris kosong)
        if (!isset($row['cif']) || !isset($row['no_angsuran'])) {
            return null;
        }

        // 2. CARI ATAU BUAT NASABAH (Cegah CIF ganda)
        // firstOrCreate: Kalau CIF sudah ada di database, ambil datanya. Kalau belum ada, buat baru.
        $nasabah = Nasabah::firstOrCreate(
            ['cif' => $row['cif']],
            [
                'nama_lengkap' => $row['nama_nasabah'],
                'nomor_hp'     => $row['no_hp'] ?? null,
            ]
        );

        // Update nomor HP nasabah lama jika sebelumnya kosong di database tapi di Excel ada
        if (empty($nasabah->nomor_hp) && !empty($row['no_hp'])) {
            $nasabah->update(['nomor_hp' => $row['no_hp']]);
        }

        // 3. LOGIKA TANGGAL PINTAR (Sangat penting agar tanggal tidak error)
        $tanggalExcel = $row['jatuh_tempo'];
        $tanggalJatuhTempo = null;

        if (is_numeric($tanggalExcel)) {
            $tanggalJatuhTempo = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalExcel)->format('Y-m-d');
        } else {
            $tanggalJatuhTempo = \Carbon\Carbon::parse($tanggalExcel)->format('Y-m-d');
        }

        // 4. LOGIKA PINJAMAN: CEK DUPLIKASI (Cegah No Angsuran Menumpuk)
        $pinjaman_lama = Pinjaman::where('no_angsuran', $row['no_angsuran'])->first();

        if ($pinjaman_lama) {
            // JIKA SUDAH ADA: Kita update datanya pakai tanggal hasil olahan
            $pinjaman_lama->update([
                'sisa_uang_pinjaman' => $row['sisa_pinjaman'],
                'tgl_jatuh_tempo'    => $tanggalJatuhTempo,
                'status_barang'      => 'Aktif'
            ]);

            // PENTING: return null agar tidak terbuat baris baru
            return null;
        }

        // 5. JIKA BELUM ADA: Buat Pinjaman Baru
        return new Pinjaman([
            'nasabah_id'         => $nasabah->id, // Mengambil ID dari proses tahap 2
            'no_angsuran'        => $row['no_angsuran'],
            'produk'             => $row['nama_produk'],
            'sisa_uang_pinjaman' => $row['sisa_pinjaman'],
            'tgl_jatuh_tempo'    => $tanggalJatuhTempo, // Memakai tanggal hasil olahan tahap 3
            'status_barang'      => 'Aktif'
        ]);
    }
}
