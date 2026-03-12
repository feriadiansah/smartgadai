<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pinjaman;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Total Uang
        $totalSisa = Pinjaman::sum('sisa_uang_pinjaman');

        // 2. Tentukan batas waktu (Besok)
        $batasWaktu = Carbon::tomorrow()->format('Y-m-d');

        // 3. Hitung jumlah yang jatuh tempo hari ini/besok untuk angka di kotak merah
        $hariIni = Pinjaman::whereDate('tgl_jatuh_tempo', '<=', $batasWaktu)->count();

        // 4. Ambil SEMUA data pinjaman untuk tabel utama (seperti biasa)
        $dataPinjaman = Pinjaman::with('nasabah')->orderBy('tgl_jatuh_tempo', 'asc')->get();

        // 5. INI YANG BARU: Ambil data KHUSUS untuk antrean WA Massal
        $dataReminder = Pinjaman::with('nasabah')
            ->whereDate('tgl_jatuh_tempo', '<=', $batasWaktu) // Syarat 1: Jatuh tempo hari ini/lewat/besok
            ->whereIn('status_barang', ['Aktif', 'Menunggu']) // Syarat 2: Belum lunas
            ->whereHas('nasabah', function($query) {
                $query->whereNotNull('nomor_hp')->where('nomor_hp', '!=', ''); // Syarat 3: Punya No HP
            })
            ->get();

        // Lempar semua variabel ke tampilan (view)
        return view('dashboard', compact('totalSisa', 'hariIni', 'dataPinjaman', 'dataReminder'));
    }
}
// < -- ? php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Pinjaman;
// use Carbon\Carbon;

// class DashboardController extends Controller
// {
//     public function index()
//     {
//         // 1. Hitung Total Uang
//         $totalSisa = Pinjaman::sum('sisa_uang_pinjaman');

//         // 2. Hitung yang jatuh tempo hari ini atau besok (H-1)
//         $hariIni = Pinjaman::whereDate('tgl_jatuh_tempo', '<=', Carbon::tomorrow())->count();

//         // 3. Ambil semua data pinjaman beserta data nasabahnya, urutkan dari yang paling dekat jatuh tempo
//         $dataPinjaman = Pinjaman::with('nasabah')->orderBy('tgl_jatuh_tempo', 'asc')->get();

//         // Lempar data ke tampilan (view)
//         return view('dashboard', compact('totalSisa', 'hariIni', 'dataPinjaman'));
//     }
// } -->
