<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// General Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PengajuanSuratController;

// Admin Controllers
use App\Http\Controllers\Admin\ProfilDesaController;
use App\Http\Controllers\Admin\JenisSuratController;
use App\Http\Controllers\Admin\ProsesPengajuanController;
use App\Http\Controllers\Admin\UserController; // Pastikan controller ini ada

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda. Rute-rute
| ini dimuat oleh RouteServiceProvider dalam sebuah grup yang
| berisi grup middleware "web".
|
*/

// RUTE PUBLIK
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// RUTE UMUM (UNTUK SEMUA USER YANG LOGIN)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// === RUTE UNTUK WARGA (setelah login) ===
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pengajuan-surat', [PengajuanSuratController::class, 'index'])->name('pengajuan.index');
    Route::post('/pengajuan-surat', [PengajuanSuratController::class, 'store'])->name('pengajuan.store');
    Route::delete('/pengajuan-surat/{pengajuanSurat}', [PengajuanSuratController::class, 'destroy'])->name('pengajuan.destroy');
});


// === RUTE KHUSUS ADMIN ===
Route::middleware(['auth', 'verified', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // Profil Desa
    Route::get('/profil-desa', [ProfilDesaController::class, 'index'])->name('profil-desa.index');
    Route::post('/profil-desa', [ProfilDesaController::class, 'update'])->name('profil-desa.update');

    // Manajemen Jenis Surat
    Route::resource('jenis-surat', JenisSuratController::class)->except(['create', 'show', 'edit']);

    // Proses Pengajuan (NAMA SUDAH DIPERBAIKI untuk menghindari konflik)
    Route::get('/proses-pengajuan', [ProsesPengajuanController::class, 'index'])->name('proses.index');
    Route::patch('/proses-pengajuan/{pengajuanSurat}', [ProsesPengajuanController::class, 'update'])->name('proses.update');
    Route::get('/proses-pengajuan/{pengajuanSurat}/cetak', [ProsesPengajuanController::class, 'cetakPdf'])->name('proses.cetak');

    // Riwayat Pengajuan
    Route::get('/pengajuan/riwayat', [ProsesPengajuanController::class, 'riwayat'])->name('pengajuan.riwayat');
    
    // Kelola Pengguna (FITUR BARU DITAMBAHKAN)
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
});


require __DIR__.'/auth.php';