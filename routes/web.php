<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Import Controller Admin
use App\Http\Controllers\Admin\JenisSuratController;
use App\Http\Controllers\PengajuanSuratController;
use App\Http\Controllers\Admin\ProsesPengajuanController;
use App\Http\Controllers\Admin\ProfilDesaController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// === RUTE UNTUK ADMIN ===
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    // Rute untuk CRUD Jenis Surat
     Route::resource('jenis-surat', JenisSuratController::class)->except(['create', 'show', 'edit']);
      // RUTE BARU UNTUK PROSES PENGAJUAN
    Route::get('/proses-pengajuan', [ProsesPengajuanController::class, 'index'])->name('pengajuan.index');
    Route::patch('/proses-pengajuan/{pengajuanSurat}', [ProsesPengajuanController::class, 'update'])->name('pengajuan.update');
    Route::get('/proses-pengajuan/{pengajuanSurat}/cetak', [ProsesPengajuanController::class, 'cetakPdf'])->name('pengajuan.cetak');
      Route::get('/profil-desa', [ProfilDesaController::class, 'index'])->name('profil-desa.index');
    Route::post('/profil-desa', [ProfilDesaController::class, 'update'])->name('profil-desa.update');
    
    // Rute admin lain bisa ditambahkan di sini
});

// === RUTE UNTUK WARGA (setelah login) ===
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pengajuan-surat', [PengajuanSuratController::class, 'index'])->name('pengajuan.index');
    Route::post('/pengajuan-surat', [PengajuanSuratController::class, 'store'])->name('pengajuan.store');
    Route::delete('/pengajuan-surat/{pengajuanSurat}', [PengajuanSuratController::class, 'destroy'])->name('pengajuan.destroy');

});

require __DIR__.'/auth.php';
