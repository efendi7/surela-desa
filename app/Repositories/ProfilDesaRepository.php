<?php
namespace App\Repositories;

use App\Repositories\Interfaces\ProfilDesaRepositoryInterface;
use App\Models\ProfilDesa;

class ProfilDesaRepository implements ProfilDesaRepositoryInterface
{
    public function getFirst(): ProfilDesa
    {
        return ProfilDesa::firstOrFail();
    }

    public function createOrGet(array $data): ProfilDesa
    {
        return ProfilDesa::firstOrCreate(['id' => 1], $data);
    }

    public function update(ProfilDesa $profilDesa, array $data): ProfilDesa
    {
        $profilDesa->update($data);
        return $profilDesa->fresh();
    }

    public function getDefaultStrukturOrganisasi(): array
    {
        return [
            ['jabatan' => 'Kepala Desa', 'nama' => ''],
            ['jabatan' => 'Badan Permusyawaratan Desa', 'nama' => ''],
            ['jabatan' => 'Sekretaris Desa', 'nama' => ''],
            ['jabatan' => 'Kaur Pemerintahan', 'nama' => ''],
            ['jabatan' => 'Kaur Pembangunan', 'nama' => ''],
            ['jabatan' => 'Kaur Pemberdayaan Masyarakat', 'nama' => ''],
            ['jabatan' => 'Kaur Kesejahteraan Rakyat', 'nama' => ''],
            ['jabatan' => 'Kaur Umum', 'nama' => ''],
            ['jabatan' => 'Kaur Keuangan', 'nama' => ''],
        ];
    }
}