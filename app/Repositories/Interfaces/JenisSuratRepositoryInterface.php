<?php

namespace App\Repositories\Interfaces;

use App\Models\JenisSurat;
use Illuminate\Support\Collection;

interface JenisSuratRepositoryInterface
{
    /**
     * Mengambil semua jenis surat dengan jumlah pengajuan aktif
     *
     * @return Collection
     */
    public function getAllWithActivePengajuanCount(): Collection;

    /**
     * Membuat jenis surat baru
     *
     * @param array $data
     * @return JenisSurat
     */
    public function create(array $data): JenisSurat;

    /**
     * Memperbarui jenis surat
     *
     * @param JenisSurat $jenisSurat
     * @param array $data
     * @return JenisSurat
     */
    public function update(JenisSurat $jenisSurat, array $data): JenisSurat;

    /**
     * Menghapus jenis surat
     *
     * @param JenisSurat $jenisSurat
     * @return bool
     */
    public function delete(JenisSurat $jenisSurat): bool;

    /**
     * Menghitung pengajuan aktif untuk jenis surat tertentu
     *
     * @param JenisSurat $jenisSurat
     * @return int
     */
    public function countActivePengajuan(JenisSurat $jenisSurat): int;

    /**
     * Mencari jenis surat berdasarkan ID
     *
     * @param int $id
     * @return JenisSurat|null
     */
    public function findById(int $id): ?JenisSurat;
}