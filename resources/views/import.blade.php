<x-app-layout>

    <x-slot name="header">
        Import Data Nasabah & Pinjaman
    </x-slot>

    <div class="max-w-4xl mx-auto py-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-8">

            <div class="flex flex-col gap-4 mb-6">
                {{-- <div>
                    <h2 class="text-2xl font-bold text-[#1b2559]">Upload File Excel</h2>
                    <p class="text-slate-500 text-sm mt-1">Gunakan template resmi agar valid untuk upload.
                    </p>
                </div> --}}
                <a href="https://docs.google.com/spreadsheets/d/10vzdEFArkBQJLs9DUaK7NkPE6bYVIO4ZooBOILR0Vhg/edit?usp=sharing" target="_blank"
                    class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold py-2 px-4 rounded-xl text-sm transition-colors flex items-center gap-2 w-max">
                    <i class="fas fa-external-link-alt"></i> Buka Template Spreadsheet
                </a>

                <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 text-sm max-w-2xl w-full">
                    <p class="font-bold mb-2">
                        <i class="fas fa-info-circle mr-1"></i> Cara Menggunakan Template:
                    </p>
                    <ol class="list-decimal list-inside space-y-1 ml-1">
                        <li>Klik tombol hijau di atas untuk membuka format template.</li>
                        <li>Di Google Sheets, klik menu <b>File</b> lalu pilih <b>Buat Salinan (Make a copy)</b>.</li>
                        <li>Isi data nasabah pada file salinan Anda (format angka akan otomatis aman).</li>
                        <li>Jika sudah selesai, klik <b>File > Download > Microsoft Excel (.xlsx)</b>.</li>
                        <li><b>Upload</b> file Excel hasil download tersebut ke form di bawah ini.</li>
                    </ol>
                </div>

            </div>
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3"></i>
                        <div>
                            <h3 class="text-sm font-bold text-red-800">Gagal mengunggah:</h3>
                            <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" class="space-y-6"
                id="upload-form">
                @csrf

                <!-- Pilih Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-bold text-[#1b2559] mb-2">1. Pilih Kategori Data
                        <span class="text-red-500">*</span></label>
                    <select name="kategori" id="kategori" required
                        class="w-full border-slate-200 rounded-xl focus:border-indigo-500 focus:ring-indigo-500 bg-slate-50 py-3 text-slate-700">
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <option value="Angsuran Rutin">📘 Angsuran Rutin (Normal)</option>
                        <option value="Lelang">📕 Kredit Bermasalah (Lelang / Macet)</option>
                    </select>
                </div>

                <!-- Input File Area dengan ID dropzone -->
                <div>
                    <label class="block text-sm font-bold text-[#1b2559] mb-2">2. Pilih File Excel (.xlsx) <span
                            class="text-red-500">*</span></label>

                    <div id="dropzone"
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:bg-slate-50 transition-all bg-white relative cursor-pointer">

                        <!-- STATE 1: SEBELUM FILE DIPILIH -->
                        <div id="upload-prompt" class="space-y-2 text-center pointer-events-none">
                            <i class="fas fa-file-excel text-4xl text-emerald-500 mb-3 block drop-shadow-sm"></i>
                            <div class="flex text-sm text-slate-600 justify-center">
                                <span class="font-bold text-indigo-600">Klik untuk memilih file</span>
                                <p class="pl-1">atau seret ke sini</p>
                            </div>
                            <p class="text-xs text-slate-500">Maksimal ukuran file 10MB (.xlsx, .xls)</p>
                        </div>

                        <!-- STATE 2: SETELAH FILE DIPILIH (Disembunyikan awalnya) -->
                        <div id="upload-success" class="hidden space-y-2 text-center w-full">
                            <div
                                class="w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-check-circle text-3xl text-indigo-600"></i> <!-- Ubah icon di sini -->
                            </div>
                            <p id="file-name" class="text-sm font-bold text-[#1b2559] truncate max-w-xs mx-auto">
                                nama_file.xlsx</p>
                            <p id="file-size" class="text-xs font-medium text-slate-500">2.5 MB</p>
                            <button type="button" id="remove-file"
                                class="text-xs text-red-500 font-bold mt-2 hover:text-red-700 transition-colors z-10 relative">
                                <i class="fas fa-times mr-1"></i> Batal / Hapus File
                            </button>
                        </div>

                        <!-- INPUT ASLINYA TETAP DISEMBUNYIKAN -->
                        <input id="file_excel" name="file_excel" type="file"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".xlsx, .xls"
                            required>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="flex items-center justify-end mt-8 border-t border-slate-100 pt-6">
                    <a href="{{ route('dashboard') }}"
                        class="mr-4 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
                        Batal
                    </a>
                    <button type="submit" id="submit-btn"
                        class="font-bold py-3 px-8 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md transition-all flex items-center gap-2">
                        <i class="fas fa-cloud-upload-alt"></i> Proses Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT UNTUK MEMBUAT UI HIDUP -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('file_excel');
            const dropzone = document.getElementById('dropzone');
            const uploadPrompt = document.getElementById('upload-prompt');
            const uploadSuccess = document.getElementById('upload-success');
            const fileNameDisplay = document.getElementById('file-name');
            const fileSizeDisplay = document.getElementById('file-size');
            const removeBtn = document.getElementById('remove-file');
            const submitBtn = document.getElementById('submit-btn');

            // FUNGSI 1: Saat file dipilih lewat klik atau drag
            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files.length > 0) {
                    const file = this.files[0];
                    showFileDetails(file);
                }
            });

            // FUNGSI 2: Efek visual saat file diseret di atas kotak
            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropzone.classList.add('border-indigo-500', 'bg-indigo-50');
            });

            dropzone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dropzone.classList.remove('border-indigo-500', 'bg-indigo-50');
            });

            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropzone.classList.remove('border-indigo-500', 'bg-indigo-50');

                if (e.dataTransfer.files.length > 0) {
                    // Masukkan file yang diseret ke dalam input file tersembunyi
                    fileInput.files = e.dataTransfer.files;
                    showFileDetails(e.dataTransfer.files[0]);
                }
            });

            // FUNGSI 3: Tombol hapus file
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Mencegah form ter-submit
                fileInput.value = ''; // Kosongkan input

                // Kembalikan tampilan ke awal
                uploadSuccess.classList.add('hidden');
                uploadPrompt.classList.remove('hidden');
                submitBtn.innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Proses Import Data';
            });

            // FUNGSI 4: Ganti UI saat submit loading
            document.getElementById('upload-form').addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang Memproses...';
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            });

            // LOGIKA UI: Menampilkan nama dan ukuran file
            // LOGIKA UI: Menampilkan nama dan ukuran file
            function showFileDetails(file) {
                // Sembunyikan prompt awal, munculkan detail file
                uploadPrompt.classList.add('hidden');
                uploadSuccess.classList.remove('hidden');

                // Tulis nama file
                fileNameDisplay.textContent = file.name;

                // Hitung ukuran file (Lebih Pintar: Cek KB atau MB)
                let sizeText = '';
                if (file.size < 1024 * 1024) {
                    // Jika ukuran di bawah 1 MB, tampilkan sebagai KB
                    let sizeInKB = (file.size / 1024).toFixed(1);
                    sizeText = sizeInKB + ' KB';
                } else {
                    // Jika ukuran 1 MB atau lebih besar, tampilkan sebagai MB
                    let sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                    sizeText = sizeInMB + ' MB';
                }

                fileSizeDisplay.textContent = sizeText;
            }
        });
    </script>
</x-app-layout>
