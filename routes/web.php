<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    LandingPageController,
    ProfileController,
    PengajuanSuratController,
    PublicProfilController,
    DashboardController as WargaDashboardController,
    WargaPengaduanController,
    WargaUmkmController,
    PublicUmkmController,
};

use App\Http\Controllers\Admin\{
    ProfilDesaController,
    JenisSuratController,
    ProsesPengajuanController,
    UserController,
    DashboardController as AdminDashboardController,
    PerangkatDesaController,
    AdminPengaduanController,
    AdminUmkmController,
};

/*
|--------------------------------------------------------------------------
| Public Routes (Guest)
|--------------------------------------------------------------------------
*/
Route::controller(LandingPageController::class)->group(function () {
    Route::get('/', 'index')->name('welcome');
    Route::get('/demo', 'demo')->name('demo');
    Route::get('/api/contact', 'contact')->name('api.contact');
    Route::get('/api/status', 'status')->name('api.status');
});

Route::get('/profil/{page}', [PublicProfilController::class, 'show'])
    ->where('page', 'sejarah|visi-misi|struktur-organisasi')
    ->name('profil.show');

Route::get('/umkm', [PublicUmkmController::class, 'index'])->name('umkm.public.index');
Route::get('/umkm/{umkm}', [PublicUmkmController::class, 'show'])
    ->where('umkm', '[0-9]+')
    ->name('umkm.public.show');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Warga)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return app(WargaDashboardController::class)->index();
    })->name('dashboard');

    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
        Route::delete('/photo', 'deletePhoto')->name('photo.destroy');
    });

    Route::controller(PengajuanSuratController::class)->prefix('pengajuan-surat')->name('pengajuan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::delete('/{pengajuan}', 'destroy')->name('destroy');
        Route::get('/{pengajuan}/lampiran/{key}', 'viewLampiran')->name('lampiran.view');
        Route::get('/{pengajuan}/download', 'download')->name('download');
        Route::delete('/riwayat/{pengajuan}', 'destroyRiwayat')->name('destroy-riwayat');
    });

    Route::controller(WargaPengaduanController::class)->prefix('pengaduan')->name('pengaduan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{pengaduan}', 'show')->name('show');
        Route::delete('/{pengaduan}', 'destroy')->name('destroy');
        Route::get('/{pengaduan}/foto/{type}', 'viewFoto')->name('foto.view')->where('type', 'bukti|proses');
        Route::get('/{pengaduan}/download/{type}', 'downloadFoto')->name('foto.download')->where('type', 'bukti|proses');
        Route::get('/{pengaduan}/timeline', 'getTimeline')->name('timeline');
    });

    // 🔹 Warga UMKM → prefix warga/umkm agar tidak bentrok dengan public
    Route::prefix('warga')->name('warga.')->group(function () {
        Route::resource('umkm', WargaUmkmController::class)
            ->parameters(['umkm' => 'umkm'])
            ->except(['show']); // tetap name warga.umkm.*
    });

    Route::get('/pengajuan-surat/{pengajuan}/download-final', [ProsesPengajuanController::class, 'downloadSuratFinal'])
         ->name('pengajuan.download-final');
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
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::controller(ProfilDesaController::class)->prefix('profil-desa')->name('profil-desa.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'update')->name('update');
        });

        Route::resource('perangkat-desa', PerangkatDesaController::class)->except(['create', 'edit', 'show']);
        Route::resource('jenis-surat', JenisSuratController::class)->except(['create', 'edit', 'show']);

        Route::controller(ProsesPengajuanController::class)
            ->prefix('proses-pengajuan')
            ->name('proses.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/riwayat', 'riwayat')->name('riwayat');
                Route::patch('/{pengajuanSurat}', 'update')->name('update');
                Route::delete('/{pengajuan}', 'destroy')->name('destroy');
                Route::get('/{pengajuan}/generate-surat', 'generateSurat')->name('generate-surat');
                Route::post('/{pengajuan}/upload-file', 'uploadFile')->name('upload-file');
                Route::delete('/{pengajuan}/hapus-file', 'hapusFile')->name('hapusFile');
                Route::post('/{pengajuan}/konfirmasi-final', 'konfirmasiFinal')->name('konfirmasiFinal');
            });

        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);

        Route::controller(AdminPengaduanController::class)->prefix('pengaduan')->name('pengaduan.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/riwayat', 'riwayat')->name('riwayat');
            Route::get('/{pengaduan}', 'show')->name('show');
            Route::delete('/{pengaduan}', 'destroy')->name('destroy');
            Route::patch('/{pengaduan}/status', 'updateStatus')->name('update.status');
            Route::patch('/{pengaduan}/update-details', 'updateDetails')->name('updateDetails');
            Route::post('/{pengaduan}/upload-proses', 'uploadProses')->name('upload.proses');
            Route::get('/{pengaduan}/foto/{type}', 'viewFoto')->name('foto.view')->where('type', 'bukti|proses');
            Route::get('/{pengaduan}/download/{type}', 'downloadFoto')->name('foto.download')->where('type', 'bukti|proses');
        });

        Route::controller(AdminUmkmController::class)->prefix('umkm')->name('umkm.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/history', 'history')->name('history');
            Route::get('/{umkm}', 'show')->name('show')->where('umkm', '[0-9]+');
            Route::patch('/{umkm}/status', 'updateStatus')->name('update.status')->where('umkm', '[0-9]+');
            Route::delete('/{umkm}', 'destroy')->name('destroy')->where('umkm', '[0-9]+');
        });

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
