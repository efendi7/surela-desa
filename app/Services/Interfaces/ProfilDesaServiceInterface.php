<?php
namespace App\Services\Interfaces;

use App\Models\ProfilDesa;
use Illuminate\Http\Request;

interface ProfilDesaServiceInterface
{
    public function getProfilDesa(): ProfilDesa;
    public function updateProfil(Request $request): ProfilDesa;
    public function handleLogoUpload(?object $file, ?string $currentLogo): ?string;
}
