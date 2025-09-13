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
use App\Http\Controllers\Admin\PerangkatDesaController;

/*
|--------------------------------------------------------------------------
| Public Routes (Guest)
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', [LandingPageController::class, 'index'])->name('welcome');

// Demo & Info Routes (from LandingPageController)
Route::get('/demo', [LandingPageController::class, 'demo'])->name('demo');
Route::get('/api/contact', [LandingPageController::class, 'contact'])->name('api.contact');
Route::get('/api/status', [LandingPageController::class, 'status'])->name('api.status');

// Public Profile Desa
Route::prefix('profil')->name('profil.')->group(function () {
    Route::get('/{page}', [PublicProfilController::class, 'show'])
        ->where('page', 'sejarah|visi-misi|struktur-organisasi')
        ->name('show');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard with role-based redirect
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::delete('/photo', [ProfileController::class, 'deletePhoto'])->name('photo.destroy');
    });

    // Warga Routes - Pengajuan Surat
    Route::prefix('pengajuan-surat')->name('pengajuan.')->group(function () {
        Route::get('/', [PengajuanSuratController::class, 'index'])->name('index');
        Route::post('/', [PengajuanSuratController::class, 'store'])->name('store');
        Route::delete('/{pengajuan}', [PengajuanSuratController::class, 'destroy'])->name('destroy');
        
        // Additional routes for pengajuan
        Route::get('/{pengajuan}/lampiran/{key}', [PengajuanSuratController::class, 'viewLampiran'])
            ->name('lampiran.view');
        Route::get('/{pengajuan}/download', [PengajuanSuratController::class, 'download'])
            ->name('download');
        Route::get('/{pengajuan}/download-final', [ProsesPengajuanController::class, 'downloadSuratFinal'])
            ->name('download-final');
        
        // Riwayat management
        Route::delete('/riwayat/{pengajuan}', [PengajuanSuratController::class, 'destroyRiwayat'])
            ->name('destroy-riwayat');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Admin Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profil Desa Management
        Route::prefix('profil-desa')->name('profil-desa.')->group(function () {
            Route::get('/', [ProfilDesaController::class, 'index'])->name('index');
            Route::post('/', [ProfilDesaController::class, 'update'])->name('update');
        });

        // Perangkat Desa Management
        Route::prefix('perangkat-desa')->name('perangkat-desa.')->group(function () {
            Route::get('/', [PerangkatDesaController::class, 'index'])->name('index');
            Route::post('/', [PerangkatDesaController::class, 'store'])->name('store');
            Route::patch('/{perangkatDesa}', [PerangkatDesaController::class, 'update'])->name('update');
            Route::delete('/{perangkatDesa}', [PerangkatDesaController::class, 'destroy'])->name('destroy');
        });

        // Jenis Surat Management
        Route::prefix('jenis-surat')->name('jenis-surat.')->group(function () {
            Route::get('/', [JenisSuratController::class, 'index'])->name('index');
            Route::post('/', [JenisSuratController::class, 'store'])->name('store');
            Route::patch('/{jenisSurat}', [JenisSuratController::class, 'update'])->name('update');
            Route::delete('/{jenisSurat}', [JenisSuratController::class, 'destroy'])->name('destroy');
        });

        // Proses Pengajuan Management
        Route::prefix('proses-pengajuan')->name('proses.')->group(function () {
            Route::get('/', [ProsesPengajuanController::class, 'index'])->name('index');
            Route::get('/riwayat', [ProsesPengajuanController::class, 'riwayat'])->name('riwayat');
            Route::patch('/{pengajuanSurat}', [ProsesPengajuanController::class, 'update'])->name('update');
            Route::delete('/{pengajuan}', [ProsesPengajuanController::class, 'destroy'])->name('destroy');
            
            // File & Document Management
            Route::get('/{pengajuan}/generate-surat', [ProsesPengajuanController::class, 'generateSurat'])
                ->name('generate-surat');
            Route::post('/{pengajuan}/upload-file', [ProsesPengajuanController::class, 'uploadFile'])
                ->name('upload-file');
            Route::delete('/{pengajuan}/hapus-file', [ProsesPengajuanController::class, 'hapusFile'])
                ->name('hapus-file');
            Route::post('/{pengajuan}/konfirmasi-final', [ProsesPengajuanController::class, 'konfirmasiFinal'])
                ->name('konfirmasi-final');
        });

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::patch('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        // API Routes for Admin
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/statistics', [DashboardController::class, 'getStatistics'])->name('statistics');
            Route::get('/recent-activities', [DashboardController::class, 'getRecentActivities'])->name('activities');
        });
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';