<?php
namespace App\Repositories\Interfaces;

use App\Models\ProfilDesa;

interface ProfilDesaRepositoryInterface
{
    public function getFirst(): ProfilDesa;
    public function createOrGet(array $data): ProfilDesa;
    public function update(ProfilDesa $profilDesa, array $data): ProfilDesa;
    public function getDefaultStrukturOrganisasi(): array;
}