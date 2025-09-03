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
use App\Http\Controllers\Admin\DashboardController;

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
        ->names('pengajuan')
        ->parameters(['pengajuan-surat' => 'pengajuan']);

    // Route tambahan untuk pengajuan surat
    Route::get('/pengajuan-surat/{pengajuan}/lampiran/{key}', [PengajuanSuratController::class, 'viewLampiran'])
        ->name('pengajuan.lampiran.view');
    
    Route::get('/pengajuan-surat/{pengajuan}/download', [PengajuanSuratController::class, 'download'])
        ->name('pengajuan.download');

    // Route untuk WARGA mengunduh suratnya yang sudah selesai (alternatif dari admin controller)
    Route::get('/pengajuan-surat/{pengajuan}/download-final', [ProsesPengajuanController::class, 'downloadSuratFinal'])
        ->name('warga.pengajuan.download');

    // Route untuk menghapus riwayat
    Route::delete('/pengajuan-surat/riwayat/{pengajuan}', [PengajuanSuratController::class, 'destroyRiwayat'])
        ->name('pengajuan.destroy-riwayat');
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
        
        // ==================================================================================
        // == RUTE BARU UNTUK GENERATE SURAT DITAMBAHKAN DI SINI ==
        // Nama rute 'pengajuan.generate' akan menjadi 'admin.pengajuan.generate' karena ada di dalam grup.
        Route::get('/pengajuan/{pengajuan}/generate-surat', [ProsesPengajuanController::class, 'generateSurat'])->name('pengajuan.generate');
        // ==================================================================================
        
        // Update status pengajuan (tanpa file)
        Route::patch('/proses-pengajuan/{pengajuanSurat}', [ProsesPengajuanController::class, 'update'])->name('proses.update');
        
     

        // Upload dan manage files
        Route::post('/pengajuan/{pengajuan}/upload-file', [ProsesPengajuanController::class, 'uploadFile'])->name('proses.uploadFile');
        Route::delete('/pengajuan/{pengajuan}/hapus-file', [ProsesPengajuanController::class, 'hapusFile'])->name('proses.hapusFile');
        Route::post('/pengajuan/{pengajuan}/konfirmasi-final', [ProsesPengajuanController::class, 'konfirmasiFinal'])->name('proses.konfirmasiFinal');

        // Riwayat dan delete
        Route::get('/proses-pengajuan/riwayat', [ProsesPengajuanController::class, 'riwayat'])->name('proses.riwayat');
        Route::delete('/proses-pengajuan/{pengajuan}', [ProsesPengajuanController::class, 'destroy'])->name('proses.destroy');

        // Kelola Pengguna
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
});

require __DIR__.'/auth.php';
