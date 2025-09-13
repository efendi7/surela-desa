<?php

// App/Services/Interfaces/ProfilDesaServiceInterface.php
namespace App\Services\Interfaces;

use App\Models\ProfilDesa;
use Illuminate\Http\Request;

interface ProfilDesaServiceInterface
{
    // Admin methods
    public function getProfilDesa(): ProfilDesa;
    public function updateProfil(Request $request): ProfilDesa;
    public function handleLogoUpload(?object $file, ?string $currentLogo): ?string;

    // Public content methods
    public function getProfilDesaArray(): array;
    public function getSejarahContent(): array;
    public function getVisiMisiContent(): array;
    public function getStrukturOrganisasiContent(): array;
    public function getContentByPage(string $page): array;
}