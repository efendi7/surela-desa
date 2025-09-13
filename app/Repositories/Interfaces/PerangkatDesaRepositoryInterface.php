<?php
// App/Repositories/Interfaces/PerangkatDesaRepositoryInterface.php
namespace App\Repositories\Interfaces;

use App\Models\PerangkatDesa;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PerangkatDesaRepositoryInterface
{
    public function getAllPaginated(int $perPage): LengthAwarePaginator;
    public function getJabatanOptions(): Collection;
    public function findById(int $id): ?PerangkatDesa;
    public function create(array $data): PerangkatDesa;
    public function update(PerangkatDesa $perangkatDesa, array $data): PerangkatDesa;
    public function delete(PerangkatDesa $perangkatDesa): void;
    
    // New methods for public display
    public function getActiveWithJabatan(): Collection;
    public function getActiveSortedByJabatan(): Collection;
}