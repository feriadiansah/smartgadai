<?php

namespace App\Imports;

use App\Models\Nasabah;
use App\Models\Pinjaman;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // PENTING: Agar baris pertama Excel dianggap judul kolom
use PhpOffice\PhpSpreadsheet\Shared\Date; // Untuk mengatasi format tanggal aneh dari Excel

class PinjamanImport implements ToCollection, WithHeadingRow
{
    protected $kategori;

    // Menerima data "Angsuran Rutin" atau "Lelang" dari form
    public function __construct($kategori)
    {
        $this->kategori = $kategori;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Abaikan baris jika kolom CIF kosong (mencegah error kalau ada baris kosong di Excel)
            if (!isset($row['cif']) || empty($row['cif'])) {
                continue;
            }

            // 1. SIMPAN NASABAH (Cari dulu, kalau belum ada baru buat baru)
            $nasabah = Nasabah::firstOrCreate(
                ['cif' => $row['cif']],
                [
                    'nama_lengkap' => $row['nama_nasabah'],
                    'nomor_hp'     => $row['no_hp'] ?? null,
                ]
            );

            // 2. URUS TANGGAL JATUH TEMPO
            $tanggalJatuhTempo = null;
            if (isset($row['jatuh_tempo'])) {
                // Konversi format tanggal ajaib Excel ke format standar Database (YYYY-MM-DD)
                $tanggalJatuhTempo = is_numeric($row['jatuh_tempo'])
                    ? Date::excelToDateTimeObject($row['jatuh_tempo'])->format('Y-m-d')
                    : date('Y-m-d', strtotime($row['jatuh_tempo']));
            }

            // 3. SIMPAN PINJAMAN / KONTRAK KE DATABASE
            Pinjaman::updateOrCreate(
                ['no_kredit' => $row['no_kontrak']], // Hindari data dobel berdasarkan nomor kontrak
                [
                    'nasabah_id'         => $nasabah->id,
                    'kategori_produk'    => $this->kategori, // Dari pilihan dropdown di web
                    'produk'             => $row['nama_produk'] ?? 'Umum',
                    'tgl_jatuh_tempo'    => $tanggalJatuhTempo,
                    'sisa_uang_pinjaman' => $row['sisa_pinjaman'] ?? 0,
                    'status_barang'      => $this->kategori == 'Lelang' ? 'Menunggu' : 'Aktif',
                ]
            );
        }
    }
}
