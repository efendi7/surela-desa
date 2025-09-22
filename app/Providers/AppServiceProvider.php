<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Interfaces & Implementations
use App\Repositories\Interfaces\ProfilDesaRepositoryInterface;
use App\Repositories\ProfilDesaRepository;
use App\Services\Interfaces\ProfilDesaServiceInterface;
use App\Services\ProfilDesaService;

use App\Repositories\Interfaces\PerangkatDesaRepositoryInterface;
use App\Repositories\PerangkatDesaRepository;
use App\Services\Interfaces\PerangkatDesaServiceInterface;
use App\Services\PerangkatDesaService;

use App\Repositories\Interfaces\PengajuanSuratRepositoryInterface;
use App\Repositories\PengajuanSuratRepository;
use App\Services\Interfaces\PengajuanSuratServiceInterface;
use App\Services\PengajuanSuratService;

use App\Repositories\Interfaces\JenisSuratRepositoryInterface;
use App\Repositories\JenisSuratRepository;
use App\Services\Interfaces\JenisSuratServiceInterface;
use App\Services\JenisSuratService;

use App\Repositories\Interfaces\ProsesPengajuanRepositoryInterface;
use App\Repositories\ProsesPengajuanRepository;
use App\Services\Interfaces\ProsesPengajuanServiceInterface;
use App\Services\ProsesPengajuanService;

use App\Services\Interfaces\TemplateServiceInterface;
use App\Services\TemplateService;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Binding Repositories
        $this->app->bind(ProfilDesaRepositoryInterface::class, ProfilDesaRepository::class);
        $this->app->bind(PerangkatDesaRepositoryInterface::class, PerangkatDesaRepository::class);
        $this->app->bind(PengajuanSuratRepositoryInterface::class, PengajuanSuratRepository::class);
        $this->app->bind(JenisSuratRepositoryInterface::class, JenisSuratRepository::class);
        $this->app->bind(ProsesPengajuanRepositoryInterface::class, ProsesPengajuanRepository::class);
        
        // Binding Services
        $this->app->bind(ProfilDesaServiceInterface::class, ProfilDesaService::class);
        $this->app->bind(PerangkatDesaServiceInterface::class, PerangkatDesaService::class);
        $this->app->bind(PengajuanSuratServiceInterface::class, PengajuanSuratService::class);
        $this->app->bind(JenisSuratServiceInterface::class, JenisSuratService::class);
        $this->app->bind(TemplateServiceInterface::class, TemplateService::class);
        $this->app->bind(ProsesPengajuanServiceInterface::class, ProsesPengajuanService::class);
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}