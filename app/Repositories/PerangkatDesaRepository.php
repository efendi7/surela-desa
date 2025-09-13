<?php

namespace App\Repositories;

use App\Models\JabatanDesa;
use App\Models\PerangkatDesa;
use App\Repositories\Interfaces\PerangkatDesaRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PerangkatDesaRepository implements PerangkatDesaRepositoryInterface
{
    public function getAllPaginated(int $perPage): LengthAwarePaginator
    {
        return PerangkatDesa::with('jabatanDesa')->latest()->paginate($perPage);
    }

    public function getJabatanOptions(): Collection
    {
        return JabatanDesa::where('is_active', true)->orderBy('urutan')->get();
    }
    
    public function findById(int $id): ?PerangkatDesa
    {
        return PerangkatDesa::find($id);
    }

    public function create(array $data): PerangkatDesa
    {
        return PerangkatDesa::create($data);
    }

    public function update(PerangkatDesa $perangkatDesa, array $data): PerangkatDesa
    {
        $perangkatDesa->update($data);
        return $perangkatDesa;
    }

    public function delete(PerangkatDesa $perangkatDesa): void
    {
        $perangkatDesa->delete();
    }

    /**
     * Get active perangkat desa with jabatan relation
     */
    public function getActiveWithJabatan(): Collection
    {
        return PerangkatDesa::with('jabatanDesa')
            ->where('status_jabatan', 'aktif')
            ->get();
    }

    /**
     * Get active perangkat desa sorted by jabatan urutan
     */
    public function getActiveSortedByJabatan(): Collection
    {
        return $this->getActiveWithJabatan()
            ->sortBy(function($perangkat) {
                return $perangkat->jabatanDesa->urutan ?? 999;
            })
            ->values();
    }
}