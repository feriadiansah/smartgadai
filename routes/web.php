<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;




// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/import', [ImportController::class, 'index'])->name('import.index');

    // Route untuk MEMPROSES file excel saat tombol submit ditekan (Nanti kita bahas)
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');

    Route::patch('/pinjaman/{id}/status', [PinjamanController::class, 'updateStatus'])->name('pinjaman.update-status');
    Route::delete('/pinjaman/hapus-massal', [App\Http\Controllers\DashboardController::class, 'hapusMassal'])->name('pinjaman.hapus-massal');
});

require __DIR__ . '/auth.php';
