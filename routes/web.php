<?php

use Illuminate\Support\Facades\Route;

// Mengelompokkan 'use' statements agar lebih rapi
use App\Http\Controllers\{
    LandingPageController,
    ProfileController,
    PengajuanSuratController,
    PublicProfilController,
    DashboardController as WargaDashboardController,
    WargaPengaduanController, // UPDATED: Menggunakan WargaPengaduanController
};

use App\Http\Controllers\Admin\{
    ProfilDesaController,
    JenisSuratController,
    ProsesPengajuanController,
    UserController,
    DashboardController as AdminDashboardController,
    PerangkatDesaController,
    AdminPengaduanController, // ADDED: Controller baru untuk admin
};

/*
|--------------------------------------------------------------------------
| Public Routes (Guest)
|--------------------------------------------------------------------------
|
| Rute yang dapat diakses oleh semua pengunjung tanpa perlu login.
|
*/

// Menggunakan Route::controller untuk LandingPageController
Route::controller(LandingPageController::class)->group(function () {
    Route::get('/', 'index')->name('welcome');
    Route::get('/demo', 'demo')->name('demo');
    Route::get('/api/contact', 'contact')->name('api.contact');
    Route::get('/api/status', 'status')->name('api.status');
});

// Public Profile Desa
Route::get('/profil/{page}', [PublicProfilController::class, 'show'])
    ->where('page', 'sejarah|visi-misi|struktur-organisasi')
    ->name('profil.show');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
|
| Rute yang memerlukan pengguna untuk login terlebih dahulu.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard dengan role-based redirect
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        // Menggunakan syntax standar untuk memanggil controller
        return app(WargaDashboardController::class)->index();
    })->name('dashboard');

    // Profile Management
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
        Route::delete('/photo', 'deletePhoto')->name('photo.destroy');
    });

    // Warga Routes - Pengajuan Surat
    Route::controller(PengajuanSuratController::class)->prefix('pengajuan-surat')->name('pengajuan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::delete('/{pengajuan}', 'destroy')->name('destroy');
        Route::get('/{pengajuan}/lampiran/{key}', 'viewLampiran')->name('lampiran.view');
        Route::get('/{pengajuan}/download', 'download')->name('download');
        Route::delete('/riwayat/{pengajuan}', 'destroyRiwayat')->name('destroy-riwayat');
    });

    // UPDATED: Warga Routes - Pengaduan menggunakan WargaPengaduanController
    Route::controller(WargaPengaduanController::class)->prefix('pengaduan')->name('pengaduan.')->group(function () {
        Route::get('/', 'index')->name('index'); // Menampilkan daftar pengaduan warga
        Route::post('/', 'store')->name('store'); // Menyimpan pengaduan baru
        Route::get('/{pengaduan}', 'show')->name('show'); // Menampilkan detail pengaduan
        Route::delete('/{pengaduan}', 'destroy')->name('destroy'); // Menghapus pengaduan
        Route::get('/{pengaduan}/foto/{type}', 'viewFoto')
            ->name('foto.view')
            ->where('type', 'bukti|proses'); // Melihat foto
        Route::get('/{pengaduan}/download/{type}', 'downloadFoto')
            ->name('foto.download')
            ->where('type', 'bukti|proses'); // Download foto
        // Di routes/web.php, tambahkan di dalam group WargaPengaduanController
        Route::get('/{pengaduan}/timeline', 'getTimeline')->name('timeline');
    });

    // Rute ini tetap di sini karena dapat diakses oleh warga setelah surat selesai
    Route::get('/pengajuan-surat/{pengajuan}/download-final', [ProsesPengajuanController::class, 'downloadSuratFinal'])
         ->name('pengajuan.download-final');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Rute khusus untuk admin, dilindungi oleh middleware 'isAdmin'.
|
*/

Route::middleware(['auth', 'verified', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Admin Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Profil Desa Management
        Route::controller(ProfilDesaController::class)->prefix('profil-desa')->name('profil-desa.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'update')->name('update');
        });

        // Perangkat Desa Management
        Route::resource('perangkat-desa', PerangkatDesaController::class)->except(['create', 'edit', 'show']);

        // Jenis Surat Management
        Route::resource('jenis-surat', JenisSuratController::class)->except(['create', 'edit', 'show']);

        // Perbaikan route untuk konsistensi naming
Route::controller(ProsesPengajuanController::class)
    ->prefix('proses-pengajuan')
    ->name('proses.') 
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/riwayat', 'riwayat')->name('riwayat');
        Route::patch('/{pengajuanSurat}', 'update')->name('update');
        Route::delete('/{pengajuan}', 'destroy')->name('destroy');
        Route::get('/{pengajuan}/generate-surat', 'generateSurat')
            ->name('generate-surat');
        Route::post('/{pengajuan}/upload-file', 'uploadFile')->name('upload-file');
        Route::delete('/{pengajuan}/hapus-file', 'hapusFile')->name('hapusFile'); // Konsisten dengan JS
        Route::post('/{pengajuan}/konfirmasi-final', 'konfirmasiFinal')->name('konfirmasiFinal'); // Konsisten dengan JS
    });
        // User Management
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);

        // UPDATED: Admin Routes - Pengaduan menggunakan AdminPengaduanController
        Route::controller(AdminPengaduanController::class)->prefix('pengaduan')->name('pengaduan.')->group(function () {
            Route::get('/', 'index')->name('index'); // admin.pengaduan.index
            Route::get('/riwayat', 'riwayat')->name('riwayat'); // admin.pengaduan.riwayat  
            Route::get('/{pengaduan}', 'show')->name('show'); // admin.pengaduan.show
            Route::delete('/{pengaduan}', 'destroy')->name('destroy'); // admin.pengaduan.destroy
            
            // Status dan Detail Updates
            Route::patch('/{pengaduan}/status', 'updateStatus')->name('update.status'); // admin.pengaduan.update.status
            Route::patch('/{pengaduan}/update-details', 'updateDetails')->name('updateDetails'); // admin.pengaduan.updateDetails
            
            // File Management
            Route::post('/{pengaduan}/upload-proses', 'uploadProses')->name('upload.proses'); // admin.pengaduan.upload.proses
            Route::get('/{pengaduan}/foto/{type}', 'viewFoto')
                ->name('foto.view') 
                ->where('type', 'bukti|proses'); // Melihat foto
            Route::get('/{pengaduan}/download/{type}', 'downloadFoto')
                ->name('foto.download')
                ->where('type', 'bukti|proses'); // Download foto
        });

        // API Routes for Admin
        Route::controller(AdminDashboardController::class)->prefix('api')->name('api.')->group(function () {
            Route::get('/statistics', 'getStatistics')->name('statistics');
            Route::get('/recent-activities', 'getRecentActivities')->name('activities');
        });
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';