<?php

namespace App\Repositories;

use App\Models\JenisSurat;
use App\Repositories\Interfaces\JenisSuratRepositoryInterface;
use Illuminate\Support\Collection;

class JenisSuratRepository implements JenisSuratRepositoryInterface
{
    /**
     * Mengambil semua jenis surat dengan jumlah pengajuan aktif
     *
     * @return Collection
     */
    public function getAllWithActivePengajuanCount(): Collection
    {
        return JenisSurat::all()->map(function ($jenisSurat) {
            $jenisSurat->active_pengajuan_count = $this->countActivePengajuan($jenisSurat);
            return $jenisSurat;
        });
    }

    /**
     * Membuat jenis surat baru
     *
     * @param array $data
     * @return JenisSurat
     */
    public function create(array $data): JenisSurat
    {
        return JenisSurat::create($data);
    }

    /**
     * Memperbarui jenis surat
     *
     * @param JenisSurat $jenisSurat
     * @param array $data
     * @return JenisSurat
     */
    public function update(JenisSurat $jenisSurat, array $data): JenisSurat
    {
        $jenisSurat->update($data);
        return $jenisSurat->fresh();
    }

    /**
     * Menghapus jenis surat
     *
     * @param JenisSurat $jenisSurat
     * @return bool
     */
    public function delete(JenisSurat $jenisSurat): bool
    {
        return $jenisSurat->delete();
    }

    /**
     * Menghitung pengajuan aktif untuk jenis surat tertentu
     *
     * @param JenisSurat $jenisSurat
     * @return int
     */
    public function countActivePengajuan(JenisSurat $jenisSurat): int
    {
        return $jenisSurat->pengajuan()
            ->whereIn('status', ['pending', 'diproses'])
            ->count();
    }

    /**
     * Mencari jenis surat berdasarkan ID
     *
     * @param int $id
     * @return JenisSurat|null
     */
    public function findById(int $id): ?JenisSurat
    {
        return JenisSurat::find($id);
    }
}