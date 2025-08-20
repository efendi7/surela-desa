<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// General Controllers
use App\Http\Controllers\LandingPageController; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PengajuanSuratController;
use App\Http\Controllers\PublicProfilController;

// Admin Controllers
use App\Http\Controllers\Admin\ProfilDesaController;
use App\Http\Controllers\Admin\JenisSuratController;
use App\Http\Controllers\Admin\ProsesPengajuanController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// RUTE PUBLIK
 Route::get('/', [LandingPageController::class, 'index'])->name('welcome');
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
    // CRUD Pengajuan oleh Warga
    Route::resource('pengajuan-surat', PengajuanSuratController::class)
        ->only(['index', 'store', 'destroy'])
        ->names('pengajuan');

    // Route untuk WARGA mengunduh suratnya yang sudah selesai
    Route::get('/pengajuan-surat/{pengajuan}/download', [ProsesPengajuanController::class, 'downloadSuratFinal'])
         ->name('warga.pengajuan.download');
});

// RUTE PROFIL DESA PUBLIK
Route::get('/profil/{page}', [PublicProfilController::class, 'show'])
    ->where('page', 'sejarah|visi-misi|struktur-organisasi')
    ->name('profil.show');

// === RUTE KHUSUS ADMIN ===
Route::middleware(['auth', 'verified', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Profil Desa
        Route::get('/profil-desa', [ProfilDesaController::class, 'index'])->name('profil-desa.index');
        Route::post('/profil-desa', [ProfilDesaController::class, 'update'])->name('profil-desa.update');

        // Manajemen Jenis Surat (CRUD)
        Route::resource('jenis-surat', JenisSuratController::class)->except(['create', 'show', 'edit']);

        // Proses Pengajuan
        Route::get('/proses-pengajuan', [ProsesPengajuanController::class, 'index'])->name('proses.index');
        
        // Update status pengajuan (tanpa file)
        Route::patch('/proses-pengajuan/{pengajuanSurat}', [ProsesPengajuanController::class, 'update'])->name('proses.update');
       
        // Route untuk admin mengunduh template Word untuk diedit
        Route::get('/proses-pengajuan/{pengajuan}/download-template', [ProsesPengajuanController::class, 'downloadTemplate'])
            ->name('proses.downloadTemplate');

             Route::post('/pengajuan/{pengajuan}/upload-file', [ProsesPengajuanController::class, 'uploadFile'])->name('proses.uploadFile');
Route::delete('/pengajuan/{pengajuan}/hapus-file', [ProsesPengajuanController::class, 'hapusFile'])->name('proses.hapusFile');
Route::post('/pengajuan/{pengajuan}/konfirmasi-final', [ProsesPengajuanController::class, 'konfirmasiFinal'])->name('proses.konfirmasiFinal');


       

     

            

        // Kelola Pengguna
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
});

require __DIR__.'/auth.php';