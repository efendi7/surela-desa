<?php

namespace App\Repositories\Interfaces;

use App\Models\PengajuanSurat;
use Illuminate\Database\Eloquent\Collection;

interface PengajuanSuratRepositoryInterface
{
    /**
     * Get pengajuan aktif untuk user tertentu
     */
    public function getActivePengajuanByUser(int $userId): Collection;

    /**
     * Get riwayat pengajuan untuk user tertentu
     */
    public function getRiwayatPengajuanByUser(int $userId): Collection;

    /**
     * Create pengajuan baru
     */
    public function create(array $data): PengajuanSurat;

    /**
     * Update pengajuan
     */
    public function update(PengajuanSurat $pengajuan, array $data): bool;

    /**
     * Delete pengajuan
     */
    public function delete(PengajuanSurat $pengajuan): bool;

    /**
     * Find pengajuan by ID dengan relasi
     */
    public function findWithRelations(int $id): ?PengajuanSurat;

    /**
     * Check apakah pengajuan dapat dibatalkan
     */
    public function canBeCancelled(PengajuanSurat $pengajuan): bool;

    /**
     * Check apakah riwayat dapat dihapus
     */
    public function canDeleteRiwayat(PengajuanSurat $pengajuan): bool;
}