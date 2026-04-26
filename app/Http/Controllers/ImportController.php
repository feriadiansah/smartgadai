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

    public function store(Request $request)
    {
        // ==========================================
        // 1. VALIDASI DASAR (BYPASS MIME TYPE)
        // Kita buang aturan 'mimes:xlsx', biarkan semua file masuk dulu
        // asalkan ukurannya tidak lebih dari 10MB
        // ==========================================
        $request->validate([
            'file_excel' => 'required|file|max:10240',
        ], [
            'file_excel.required' => 'File tidak boleh kosong!',
            'file_excel.max'      => 'Ukuran file tidak boleh lebih dari 10 MB',
        ]);

        // ==========================================
        // 2. PENGECEKAN MANUAL (ANTI-ERROR LAPTOP)
        // Kita paksa baca ujung nama filenya saja (misal: "template.xlsx" -> "xlsx")
        // ==========================================
        $file = $request->file('file_excel');
        $ekstensi = strtolower($file->getClientOriginalExtension());

        // Kalau ekstensinya bukan xlsx, xls, atau csv, baru kita tolak!
        if (!in_array($ekstensi, ['xlsx', 'xls', 'csv'])) {
            return back()->withErrors(['file_excel' => 'File harus berupa Excel (.xlsx atau .xls)']);
        }

        // ==========================================
        // 3. PROSES IMPORT JIKA AMAN
        // ==========================================
        try {
            Excel::import(new PinjamanImport(), $file);

            return redirect()->route('dashboard')->with('success', 'Data Nasabah & Pinjaman berhasil diimpor!');
        } catch (\Exception $e) {
            // TAMPILKAN ERROR ASLINYA KE LAYAR JIKA DATA EXCEL BERANTAKAN
            dd($e->getMessage());
        }
    }
}
