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

        // 5. Ambil data KHUSUS untuk antrean WA Massal
        $dataReminder = Pinjaman::with('nasabah')
            ->whereDate('tgl_jatuh_tempo', '<=', $batasWaktu) // Syarat 1: Jatuh tempo hari ini/lewat/besok
            ->whereIn('status_barang', ['Aktif', 'Menunggu']) // Syarat 2: Belum lunas
            ->whereHas('nasabah', function ($query) {
                $query->whereNotNull('nomor_hp')->where('nomor_hp', '!=', ''); // Syarat 3: Punya No HP
            })
            ->get();

        // 6. --- REFACTOR: Rakit Link WA di Controller ---
        // Kita tempelkan variabel baru bernama 'link_wa' ke data yang akan dikirim ke View
        $dataPinjaman->each(function ($item) {
            $item->link_wa = $this->generateWaLink($item);
        });

        $dataReminder->each(function ($item) {
            $item->link_wa = $this->generateWaLink($item);
        });

        // Lempar semua variabel ke tampilan (view)
        return view('dashboard', compact('totalSisa', 'hariIni', 'dataPinjaman', 'dataReminder'));
    }

    // FUNGSI PRIVATE: Khusus untuk merakit format pesan WA Pegadaian
    private function generateWaLink($pinjaman)
    {
        $noHp = $pinjaman->nasabah->nomor_hp;

        // Kalau tidak ada nomor HP, kembalikan tanda '#'
        if (!$noHp) return "#";

        // Bersihkan Nomor HP
        $noHp = preg_replace('/[^0-9]/', '', $noHp);
        if (substr($noHp, 0, 1) == '0') {
            $noHp = '62' . substr($noHp, 1);
        }

        // Format Pesan Pegadaian
        $pesan = "Yth Bpk/Ibu " . strtoupper($pinjaman->nasabah->nama_lengkap) . "\n\n";
        $pesan .= "Angsuran Kredit Produk " . $pinjaman->produk . "\n";
        $pesan .= "No Angsuran; " . $pinjaman->no_angsuran . "\n";
        $pesan .= "Akan Jatuh Tempo Tanggal: " . Carbon::parse($pinjaman->tgl_jatuh_tempo)->translatedFormat('d M Y') . "\n\n";
        $pesan .= "Abaikan pesan ini jika sudah melakukan pembayaran\n\n";
        $pesan .= "Transaksi Semakin Mudah; https://tring.onelink.me/rIEN/infoPegadaian\n\n";
        $pesan .= "Info\nPEGADAIAN KEBAYORAN BARU";

        return "https://web.whatsapp.com/send?phone=" . $noHp . "&text=" . urlencode($pesan);
    }
    public function hapusMassal()
    {
        \App\Models\Pinjaman::query()->delete();
        return redirect()->back()->with('success', 'Semua data pinjaman berhasil dikosongkan!');
    }
}





// 1
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
