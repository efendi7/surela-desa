<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\PengajuanSuratRepositoryInterface;
use App\Repositories\PengajuanSuratRepository;
use App\Repositories\Interfaces\JenisSuratRepositoryInterface; // <-- Perbaikan typo Repostiories -> Repositories
use App\Repositories\JenisSuratRepository;
use App\Services\Interfaces\PengajuanSuratServiceInterface;
use App\Services\PengajuanSuratService;
use App\Services\Interfaces\JenisSuratServiceInterface;
use App\Services\JenisSuratService;
use App\Services\Interfaces\TemplateServiceInterface;
use App\Services\TemplateService;
use App\Repositories\Interfaces\ProfilDesaRepositoryInterface;
use App\Services\Interfaces\ProfilDesaServiceInterface;
use App\Repositories\ProfilDesaRepository;
use App\Services\ProfilDesaService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // --- BAGIAN YANG DIPERBAIKI ---
        // Setiap binding (interface ke class) harus didaftarkan secara terpisah.

        // Binding Repositories
        $this->app->bind(PengajuanSuratRepositoryInterface::class, PengajuanSuratRepository::class);
        $this->app->bind(JenisSuratRepositoryInterface::class, JenisSuratRepository::class);
        $this->app->bind(ProfilDesaRepositoryInterface::class, ProfilDesaRepository::class);

        // Binding Services
        $this->app->bind(PengajuanSuratServiceInterface::class, PengajuanSuratService::class);
        $this->app->bind(JenisSuratServiceInterface::class, JenisSuratService::class);
        $this->app->bind(TemplateServiceInterface::class, TemplateService::class);
        $this->app->bind(ProfilDesaServiceInterface::class,ProfilDesaService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
