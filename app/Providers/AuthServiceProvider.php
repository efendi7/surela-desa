<?php

namespace App\Providers;

use App\Models\PengajuanSurat; // <-- Import model
use App\Policies\PengajuanSuratPolicy; // <-- Import policy

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PengajuanSurat::class => PengajuanSuratPolicy::class, // <-- TAMBAHKAN BARIS INI
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}