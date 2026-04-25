<?php

namespace App\Imports;

use App\Models\Nasabah;
use App\Models\Pinjaman;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class PinjamanImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 1. CEK JUDUL KOLOM
        if (!isset($row['cif']) || !isset($row['no_angsuran'])) {
            return null;
        }

        // 2. CARI ATAU BUAT NASABAH
        $nasabah = Nasabah::firstOrCreate(
            ['cif' => $row['cif']],
            [
                'nama_lengkap' => $row['nama_nasabah'],
                'nomor_hp'     => $row['no_hp'] ?? null,
            ]
        );

        if (empty($nasabah->nomor_hp) && !empty($row['no_hp'])) {
            $nasabah->update(['nomor_hp' => $row['no_hp']]);
        }

        // 3. LOGIKA TANGGAL PINTAR (Diperbarui untuk mengatasi format DD/MM/YYYY)
        $tanggalExcel = $row['jatuh_tempo'];
        $tanggalJatuhTempo = null;

        if (is_numeric($tanggalExcel)) {
            // Jika formatnya angka bawaan Excel asli
            $tanggalJatuhTempo = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalExcel)->format('Y-m-d');
        } elseif (str_contains($tanggalExcel, '/')) {
            // Jika formatnya dari sistem lama pakai garis miring (contoh: 25/05/2026)
            // Hati-hati, format Indonesia itu DD/MM/YYYY
            $tanggalJatuhTempo = Carbon::createFromFormat('d/m/Y', $tanggalExcel)->format('Y-m-d');
        } else {
            // Jika formatnya sudah YYYY-MM-DD atau format teks standar lain
            $tanggalJatuhTempo = Carbon::parse($tanggalExcel)->format('Y-m-d');
        }

       // ==========================================
        // FITUR BARU: TUKANG SAPU ANGKA KOTOR (SUPER LEVEL)
        // ==========================================
        // Tangkap data aslinya (misal: "Rp. ,167,700.00" atau "rp 2,500,000")
        $sisa_kotor = $row['sisa_pinjaman'];

        // VACCUM CLEANER REGEX: Hapus semua BUKAN angka (0-9) dan BUKAN titik (.)
        $sisa_bersih_string = preg_replace('/[^0-9.]/', '', $sisa_kotor);

        // Ubah jadi angka mutlak (Float agar desimal .00 di belakang tetap aman)
        // Jika hasil regex kosong (karena admin tidak isi apa-apa), jadikan 0
        $sisa_pinjaman_bersih = $sisa_bersih_string === '' ? 0 : (float) $sisa_bersih_string;
        // ==========================================


        // 4. LOGIKA PINJAMAN: CEK DUPLIKASI
        $pinjaman_lama = Pinjaman::where('no_angsuran', $row['no_angsuran'])->first();

        if ($pinjaman_lama) {
            // JIKA DATA LAMA: Update nominal & tanggal SAJA.
            $pinjaman_lama->update([
                'sisa_uang_pinjaman' => $sisa_pinjaman_bersih, // <--- Pakai yang sudah bersih
                'tgl_jatuh_tempo'    => $tanggalJatuhTempo
            ]);

            return null;
        }

        // 5. JIKA DATA BARU: Masukkan sebagai Pinjaman Baru dengan status default 'Belum'
        return new Pinjaman([
            'nasabah_id'         => $nasabah->id,
            'no_angsuran'        => $row['no_angsuran'],
            'produk'             => $row['nama_produk'],
            'sisa_uang_pinjaman' => $sisa_pinjaman_bersih, // <--- Pakai yang sudah bersih
            'tgl_jatuh_tempo'    => $tanggalJatuhTempo,
            'status_barang'      => 'Belum'
        ]);
    }
}
