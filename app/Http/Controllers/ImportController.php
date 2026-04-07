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
        // 1. MANDOR MENGECEK BARANG BAWAAN (Validasi)
        $request->validate([
            'kategori' => 'required|string',
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240', // Maks 10MB
        ], [
            // Pesan error pakai bahasa Indonesia biar gampang dimengerti
            'file_excel.mimes' => 'File harus berupa Excel (.xlsx atau .xls)',
            'file_excel.max' => 'Ukuran file tidak boleh lebih dari 10 MB',
        ]);

        try {
            Excel::import(new PinjamanImport($request->kategori), $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data berhasil diimport');
        } catch (\Exception $e) {
            // TAMPILKAN ERROR ASLINYA KE LAYAR
            dd($e->getMessage());
        }
    }
}
