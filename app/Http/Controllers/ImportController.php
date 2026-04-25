<?php

namespace App\Http\Controllers;

use App\Imports\PinjamanImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        return view('import'); // Akan mencari file resources/views/import.blade.php
    }

    // Fungsi store() biarkan kosong dulu, nanti untuk memproses Excel-nya
    public function store(Request $request)
    {
        // 1. MANDOR DASAR: Cuma cek apakah ada file & ukurannya maksimal 10MB
        // (Kita buang aturan 'mimes' dari sini!)
        $request->validate([
            'file_excel' => 'required|file|max:10240',
        ], [
            'file_excel.required' => 'File tidak boleh kosong!',
            'file_excel.max'      => 'Ukuran file tidak boleh lebih dari 10 MB',
        ]);

        // 2. PENGECEKAN MANUAL (JURUS BYPASS OS LAPTOP)
        // Kita ambil ekstensi murni dari nama filenya (misal dari "template.xlsx" diambil "xlsx"-nya)
        $file = $request->file('file_excel');
        $ekstensi = strtolower($file->getClientOriginalExtension());

        // Cek apakah ekstensinya ada di daftar yang kita izinkan
        if (!in_array($ekstensi, ['xlsx', 'xls', 'csv'])) {
            // Kalau bukan, tendang balik ke halaman sebelumnya dengan pesan error merah!
            return back()->withErrors(['file_excel' => 'File harus berupa Excel (.xlsx atau .xls)']);
        }

        // 3. PROSES IMPORT JIKA EKSTENSI AMAN
        try {
            Excel::import(new PinjamanImport(), $file);

            return redirect()->route('dashboard')->with('success', 'Data Nasabah & Pinjaman berhasil diimpor!');
        } catch (\Exception $e) {
            // TAMPILKAN ERROR ASLINYA KE LAYAR
            dd($e->getMessage());
        }
    }
}
