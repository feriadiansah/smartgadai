<x-app-layout>
    <x-slot name="header">
        Dashboard Reminder Tagihan
    </x-slot>

    <div x-data="{ showModalWA: false }">

        <!-- KARTU STATISTIK -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 mt-2">
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 border-l-4 border-l-blue-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Total Sisa Pinjaman (Aktif)</p>
                        <h3 class="text-2xl font-bold text-[#1b2559]">Rp {{ number_format($totalSisa, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div
                        class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 text-xl">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 border-l-4 border-l-red-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Perlu di-Reminder Hari Ini</p>
                        <h3 class="text-2xl font-bold text-red-600">{{ $hariIni }} Nasabah</h3>
                        <p class="text-xs text-slate-400 mt-1">H-1 s/d Jatuh Tempo</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 text-xl">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 border-l-4 border-l-emerald-500">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-semibold text-slate-500 mb-1">Data Excel Terakhir Diperbarui</p>
                        <h3 class="text-lg font-bold text-[#1b2559] mt-1">{{ \Carbon\Carbon::now()->format('d M Y') }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::now()->format('H:i') }} WIB</p>
                    </div>
                    <div
                        class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500 text-xl cursor-pointer hover:bg-emerald-100 transition-colors">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- AREA TABEL -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div
                class="p-6 border-b border-slate-100 flex flex-col lg:flex-row justify-between items-center gap-4 bg-white">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-bold text-[#1b2559]">Daftar Nasabah Jatuh Tempo</h2>
                    <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">Hari Ini</span>
                </div>

                <div class="flex items-center gap-3 w-full lg:w-auto">
                    <div class="relative flex-1 lg:w-64">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400"></i>
                        <input type="text" id="searchInput" onkeyup="cariCIF()" placeholder="Cari Nomor CIF..."
                            class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all bg-slate-50">
                    </div>

                    <!-- Tombol Pemicu Modal Antrean WA -->
                    <form action="{{ route('pinjaman.hapus-massal') }}" method="POST" class="inline-block"
                        onsubmit="return confirm('AWAS! Apakah Anda yakin ingin menghapus SEMUA data tagihan ini? Tindakan ini akan menyembunyikan data dari Dashboard.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-4 py-2 rounded-xl font-semibold text-sm flex items-center gap-2 transition-colors shadow-sm">
                            <i class="fas fa-trash-alt"></i> Kosongkan Data
                        </button>
                    </form>

                    <button @click="showModalWA = true"
                        class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-xl font-semibold text-sm flex items-center gap-2 transition-colors shadow-sm">
                        <i class="fab fa-whatsapp text-lg"></i> Kirim WA Massal ({{ count($dataReminder) }})
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto min-h-[250px] pb-16">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/50 text-slate-500 font-bold uppercase text-[11px]">
                        <tr>
                            <th class="px-6 py-4 border-b border-slate-100">Nasabah</th>
                            <th class="px-6 py-4 border-b border-slate-100">No Angsuran</th>
                            <th class="px-6 py-4 border-b border-slate-100">Produk</th>
                            <th class="px-6 py-4 border-b border-slate-100">Tgl Jatuh Tempo</th>
                            <th class="px-6 py-4 border-b border-slate-100 text-right">Sisa Pinjaman</th>
                            <th class="px-6 py-4 border-b border-slate-100 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($dataPinjaman as $pinjaman)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-[#1b2559]">{{ $pinjaman->nasabah->nama_lengkap }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="text-[10px] text-slate-500 font-mono bg-slate-100 px-1.5 py-0.5 rounded">CIF:
                                            {{ $pinjaman->nasabah->cif }}</span>

                                        <!-- TOMBOL WA SATUAN (SUPER BERSIH KARENA LOGIKA DI CONTROLLER) -->
                                        @if ($pinjaman->nasabah->nomor_hp)
                                            <a href="{{ $pinjaman->link_wa }}" target="_blank"
                                                class="text-xs text-emerald-600 hover:text-emerald-800 hover:bg-emerald-100 font-bold inline-flex items-center gap-1 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md transition-colors shadow-sm">
                                                <i class="fab fa-whatsapp text-sm"></i> Kirim Pesan
                                            </a>
                                        @else
                                            <span
                                                class="text-xs text-red-400 font-medium inline-flex items-center gap-1 bg-red-50 border border-red-100 px-2 py-0.5 rounded-md">
                                                <i class="fas fa-phone-slash"></i> No HP Kosong
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-mono text-slate-600 font-medium">{{ $pinjaman->no_angsuran }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        class="bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border border-indigo-100">
                                        {{ $pinjaman->produk }}
                                    </span>
                                </td>

                                <td
                                    class="px-6 py-4 font-bold {{ $pinjaman->tgl_jatuh_tempo <= now()->format('Y-m-d') ? 'text-red-600' : 'text-slate-700' }}">
                                    {{ \Carbon\Carbon::parse($pinjaman->tgl_jatuh_tempo)->format('d M Y') }}
                                </td>

                                <td class="px-6 py-4 text-right font-bold text-[#1b2559]">Rp
                                    {{ number_format($pinjaman->sisa_uang_pinjaman, 0, ',', '.') }}</td>

                                <!-- Kolom 6: STATUS (Kodingan Smart Dropdown) -->
                                <td class="px-6 py-4 text-center">
                                    <div x-data="{ open: false, currentStatus: '{{ $pinjaman->status_barang }}' }" class="relative inline-block text-left">
                                        <button @click="open = !open" type="button"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 shadow-sm border"
                                            :class="{
                                                'bg-amber-100 text-amber-700 border-amber-200 hover:bg-amber-200': currentStatus === 'Menunggu',
                                                'bg-blue-100 text-blue-700 border-blue-200 hover:bg-blue-200': currentStatus === 'Diperpanjang',
                                                'bg-emerald-100 text-emerald-700 border-emerald-200 hover:bg-emerald-200': currentStatus === 'Lunas',
                                                'bg-red-100 text-red-700 border-red-200 hover:bg-red-200': currentStatus === 'Lelang'
                                            }">
                                            <span x-text="currentStatus"></span>
                                            <i class="fas fa-chevron-down ml-1.5 text-[8px] opacity-70"></i>
                                        </button>

                                        <div x-show="open" @click.away="open = false" style="display: none;"
                                            class="absolute right-0 w-36 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden {{ $loop->remaining <= 1 && $loop->count > 2 ? 'bottom-full mb-2 origin-bottom-right' : 'mt-2 origin-top-right' }}">
                                            <div class="py-1">
                                                <button
                                                    @click="updateStatusDB({{ $pinjaman->id }}, 'Menunggu'); currentStatus = 'Menunggu'; open = false"
                                                    class="block w-full text-left px-4 py-2 text-xs text-amber-700 hover:bg-amber-50 font-semibold transition-colors">Menunggu</button>
                                                <button
                                                    @click="updateStatusDB({{ $pinjaman->id }}, 'Diperpanjang'); currentStatus = 'Diperpanjang'; open = false"
                                                    class="block w-full text-left px-4 py-2 text-xs text-blue-700 hover:bg-blue-50 font-semibold transition-colors">Diperpanjang</button>
                                                <button
                                                    @click="updateStatusDB({{ $pinjaman->id }}, 'Lunas'); currentStatus = 'Lunas'; open = false"
                                                    class="block w-full text-left px-4 py-2 text-xs text-emerald-700 hover:bg-emerald-50 font-semibold transition-colors">Lunas</button>
                                                <button
                                                    @click="updateStatusDB({{ $pinjaman->id }}, 'Lelang'); currentStatus = 'Lelang'; open = false"
                                                    class="block w-full text-left px-4 py-2 text-xs text-red-700 hover:bg-red-50 font-semibold transition-colors">Dilelang
                                                    / Hangus</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada data
                                    pinjaman.
                                    Silakan upload Excel terlebih dahulu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL POPUP ANTREAN WA MASSAL -->
    <!-- ========================================== -->
    <div x-show="showModalWA" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModalWA" x-transition.opacity
                class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" @click="showModalWA = false">
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showModalWA" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">

                <!-- Header Modal -->
                <div class="bg-green-500 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-white flex items-center gap-2" id="modal-title">
                        <i class="fab fa-whatsapp text-2xl"></i> Antrean Pengiriman WA ({{ count($dataReminder) }}
                        Orang)
                    </h3>
                    <button @click="showModalWA = false" class="text-white hover:text-green-200 focus:outline-none">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Isi Modal (Daftar Antrean) -->
                <div class="bg-slate-50 px-6 py-4 max-h-[60vh] overflow-y-auto">
                    @if (count($dataReminder) > 0)
                        <p class="text-sm text-slate-600 mb-4">Silakan klik tombol <b>"Kirim"</b> satu per satu
                            dari atas ke bawah untuk membuka WhatsApp Web secara otomatis.</p>

                        <div class="space-y-3">
                            @foreach ($dataReminder as $index => $reminder)
                                <!-- Kartu Antrean per Orang -->
                                <div class="bg-white border border-slate-200 p-4 rounded-xl flex justify-between items-center shadow-sm hover:border-green-300 transition-colors"
                                    x-data="{ dikirim: false }">
                                    <div>
                                        <p class="font-bold text-[#1b2559]">{{ $index + 1 }}.
                                            {{ $reminder->nasabah->nama_lengkap }}</p>
                                        <p class="text-xs text-slate-500">Jatuh Tempo: <span
                                                class="text-red-500 font-bold">{{ \Carbon\Carbon::parse($reminder->tgl_jatuh_tempo)->format('d M Y') }}</span>
                                            | Tagihan: Rp
                                            {{ number_format($reminder->sisa_uang_pinjaman, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <!-- Indikator kalau sudah diklik -->
                                        <span x-show="dikirim" class="text-xs font-bold text-green-600"
                                            style="display: none;"><i class="fas fa-check-circle"></i>
                                            Selesai</span>

                                        <!-- TOMBOL WA MASSAL (SUPER BERSIH) -->
                                        <a href="{{ $reminder->link_wa }}" target="_blank" @click="dikirim = true"
                                            class="bg-green-100 text-green-700 hover:bg-green-500 hover:text-white border border-green-200 px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
                                            <i class="fab fa-whatsapp"></i> Kirim
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <i class="fas fa-check-circle text-5xl text-emerald-400 mb-3"></i>
                            <h4 class="text-lg font-bold text-slate-700">Tugas Selesai!</h4>
                            <p class="text-sm text-slate-500 mt-1">Tidak ada nasabah yang perlu di-reminder hari
                                ini.</p>
                        </div>
                    @endif
                </div>

                <!-- Footer Modal -->
                <div class="bg-white px-6 py-4 border-t border-slate-200 flex justify-end">
                    <button @click="showModalWA = false"
                        class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-6 rounded-lg transition-colors">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    </div> <!-- END ALPINE JS WRAPPER -->

    <!-- SCRIPT AJAX STATUS -->
    <script>
        // 1. Fungsi Update Status (Kode lama kamu)
        function updateStatusDB(pinjamanId, statusBaru) {
            let csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            fetch(`/pinjaman/${pinjamanId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        status: statusBaru
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengupdate status.');
                });
        }

        // 2. FUNGSI BARU: Pencarian Nama Nasabah
        function cariCIF() {
            // Ambil nomor CIF yang diketik admin
            let input = document.getElementById("searchInput").value.toLowerCase();
            let barisTabel = document.querySelectorAll("tbody tr");

            // Cek setiap baris satu per satu
            barisTabel.forEach(function(baris) {
                // TARGET BERUBAH: Sekarang kita mencari elemen <span> yang punya class 'font-mono'
                // (Karena di situlah kamu menaruh teks "CIF: 12345...")
                let kolomCIF = baris.querySelector("td span.font-mono");

                if (kolomCIF) {
                    // Ambil teks di dalam span tersebut
                    let teksCIF = kolomCIF.innerText.toLowerCase();

                    // Jika nomor yang diketik cocok dengan nomor CIF di baris ini, tampilkan!
                    if (teksCIF.includes(input)) {
                        baris.style.display = "";
                    } else {
                        baris.style.display = "none";
                    }
                }
            });
        }
    </script>
</x-app-layout>
