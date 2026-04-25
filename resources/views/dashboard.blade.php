<x-app-layout>
    <x-slot name="header">
        Dashboard Reminder Tagihan
    </x-slot>

    <div x-data="{ showModalWA: false }">

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

                        @php
                            $terakhirUpdate = \App\Models\Pinjaman::max('updated_at');
                        @endphp

                        @if ($terakhirUpdate)
                            <h3 class="text-lg font-bold text-[#1b2559] mt-1">
                                {{ \Carbon\Carbon::parse($terakhirUpdate)->translatedFormat('d M Y') }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ \Carbon\Carbon::parse($terakhirUpdate)->format('H:i') }} WIB
                            </p>
                        @else
                            <h3 class="text-lg font-bold text-[#1b2559] mt-1">Belum Ada</h3>
                            <p class="text-xs text-slate-400 mt-1">-</p>
                        @endif
                    </div>
                    <div
                        class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500 text-xl cursor-pointer hover:bg-emerald-100 transition-colors">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                </div>
            </div>
        </div>

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
                        <input type="text" id="searchInput" onkeyup="filterTabel()" placeholder="Cari Nomor CIF..."
                            class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all bg-slate-50">
                    </div>

                    <select id="filterStatus" onchange="filterTabel()"
                        class="border border-slate-200 rounded-xl px-4 py-2 text-sm bg-slate-50 outline-none focus:border-indigo-500 text-slate-700 font-semibold cursor-pointer">
                        <option value="semua">Semua Status</option>
                        <option value="sudah">✅ Sudah Diingatkan</option>
                        <option value="belum">⏳ Belum Diingatkan</option>
                    </select>

                    <form action="{{ route('pinjaman.hapus-massal') }}" method="POST" id="formHapusMassal"
                        class="w-1/2 md:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="konfirmasiHapus()"
                            class="w-full justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-2 rounded-xl font-semibold text-sm flex items-center gap-2 transition-colors shadow-sm">
                            <i class="fas fa-trash-alt"></i> <span class="hidden sm:inline">Hapus Semua</span><span
                                class="sm:hidden">Hapus</span>
                        </button>
                    </form>
                    <button @click="showModalWA = true"
                        class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-xl font-semibold text-sm flex items-center gap-2 transition-colors shadow-sm">
                        <i class="fab fa-whatsapp text-lg"></i> Kirim WA Massal ({{ count($dataReminder) }})
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto overflow-y-auto max-h-[500px] min-h-[250px] pb-16 border-b border-slate-100">
                <table class="w-full text-left text-sm relative">

                    <thead
                        class="bg-slate-50 text-slate-500 font-bold uppercase text-[11px] sticky top-0 z-10 shadow-sm outline outline-1 outline-slate-100">
                        <tr>
                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50">Nasabah</th>
                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50">No Angsuran</th>
                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50">Produk</th>
                            <th class="px-6 py-4 border-b border-slate-100 bg-slate-50">Tgl Jatuh Tempo</th>
                            <th class="px-6 py-4 border-b border-slate-100 text-right bg-slate-50">Sisa Pinjaman</th>
                            <th class="px-6 py-4 border-b border-slate-100 text-center bg-slate-50">Status</th>
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

                                        @if ($pinjaman->nasabah->nomor_hp)
                                            <a href="{{ $pinjaman->link_wa }}" target="_blank"
                                                onclick="tandaiSudah({{ $pinjaman->id }}, this)"
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

                                <td class="px-6 py-4 text-center">
                                    @if ($pinjaman->status_barang == 'Sudah')
                                        <span id="badge-status-{{ $pinjaman->id }}"
                                            class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase border border-green-200 status-teks">
                                            <i class="fas fa-check-circle mr-1"></i> Sudah
                                        </span>
                                    @else
                                        <span id="badge-status-{{ $pinjaman->id }}"
                                            class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase border border-amber-200 status-teks">
                                            <i class="fas fa-clock mr-1"></i> Belum
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada data pinjaman.
                                    Silakan upload Excel terlebih dahulu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

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

                    <div class="bg-green-500 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg leading-6 font-bold text-white flex items-center gap-2" id="modal-title">
                            <i class="fab fa-whatsapp text-2xl"></i> Antrean Pengiriman WA ({{ count($dataReminder) }}
                            Orang)
                        </h3>
                        <button @click="showModalWA = false"
                            class="text-white hover:text-green-200 focus:outline-none">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 max-h-[60vh] overflow-y-auto">
                        @if (count($dataReminder) > 0)
                            <p class="text-sm text-slate-600 mb-4">Silakan klik tombol <b>"Kirim"</b> satu per satu
                                dari atas ke bawah untuk membuka WhatsApp Web secara otomatis.</p>

                            <div class="space-y-3">
                                @foreach ($dataReminder as $index => $reminder)
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
                                            <span x-show="dikirim" class="text-xs font-bold text-green-600"
                                                style="display: none;"><i class="fas fa-check-circle"></i>
                                                Selesai</span>

                                            <a href="{{ $reminder->link_wa }}" target="_blank"
                                                @click="dikirim = true"
                                                onclick="tandaiSudah({{ $reminder->id }}, this)"
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

                    <div class="bg-white px-6 py-4 border-t border-slate-200 flex justify-end">
                        <button @click="showModalWA = false"
                            class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-6 rounded-lg transition-colors">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // FUNGSI UNTUK MENGUBAH STATUS OTOMATIS SAAT TOMBOL WA DIKLIK
        function tandaiSudah(pinjamanId, tombolEl) {
            let csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch(`/pinjaman/${pinjamanId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        status: 'Sudah'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let badge = document.getElementById(`badge-status-${pinjamanId}`);
                        if (badge) {
                            badge.className =
                                "bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase border border-green-200 status-teks";
                            badge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Sudah';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // FUNGSI GABUNGAN: PENCARIAN CIF & FILTER STATUS DROPDOWN
        function filterTabel() {
            let inputCIF = document.getElementById("searchInput").value.toLowerCase();
            let inputStatus = document.getElementById("filterStatus").value.toLowerCase();
            let barisTabel = document.querySelectorAll("tbody tr");

            barisTabel.forEach(function(baris) {
                let kolomCIF = baris.querySelector("td span.font-mono");
                let kolomStatus = baris.querySelector("td span.status-teks");

                if (kolomCIF && kolomStatus) {
                    let teksCIF = kolomCIF.innerText.toLowerCase();
                    let teksStatus = kolomStatus.innerText.toLowerCase();

                    let matchCIF = teksCIF.includes(inputCIF);
                    let matchStatus = (inputStatus === "semua") || teksStatus.includes(inputStatus);

                    if (matchCIF && matchStatus) {
                        baris.style.display = "";
                    } else {
                        baris.style.display = "none";
                    }
                }
            });
        }

        // FUNGSI POP-UP SWEETALERT UNTUK KOSONGKAN DATA
        function konfirmasiHapus() {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Semua data tagihan akan di hapus dari Dashboard!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Kosongkan!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formHapusMassal').submit();
                }
            })
        }
    </script>
</x-app-layout>
