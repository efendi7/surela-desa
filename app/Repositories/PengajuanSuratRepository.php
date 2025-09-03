<?php

namespace App\Repositories;

use App\Models\PengajuanSurat;
use App\Repositories\Interfaces\PengajuanSuratRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PengajuanSuratRepository implements PengajuanSuratRepositoryInterface
{
    /**
     * Get pengajuan aktif untuk user tertentu
     */
    public function getActivePengajuanByUser(int $userId): Collection
    {
        return PengajuanSurat::where('user_id', $userId)
            ->with(['jenisSurat:id,nama_surat', 'user:id,name,email,nik,address'])
            ->whereIn('status', ['pending', 'diproses'])
            ->latest()
            ->get();
    }

    /**
     * Get riwayat pengajuan untuk user tertentu
     */
    public function getRiwayatPengajuanByUser(int $userId): Collection
    {
        return PengajuanSurat::where('user_id', $userId)
            ->with(['jenisSurat:id,nama_surat', 'user:id,name,email,nik,address'])
            ->whereIn('status', ['selesai', 'ditolak'])
            ->latest()
            ->get();
    }

    /**
     * Create pengajuan baru
     */
    public function create(array $data): PengajuanSurat
    {
        return PengajuanSurat::create($data);
    }

    /**
     * Update pengajuan
     */
    public function update(PengajuanSurat $pengajuan, array $data): bool
    {
        return $pengajuan->update($data);
    }

    /**
     * Delete pengajuan
     */
    public function delete(PengajuanSurat $pengajuan): bool
    {
        return $pengajuan->delete();
    }

    /**
     * Find pengajuan by ID dengan relasi
     */
    public function findWithRelations(int $id): ?PengajuanSurat
    {
        return PengajuanSurat::with(['jenisSurat', 'user'])
            ->find($id);
    }

    /**
     * Check apakah pengajuan dapat dibatalkan
     */
    public function canBeCancelled(PengajuanSurat $pengajuan): bool
    {
        return in_array($pengajuan->status, ['pending']);
    }

    /**
     * Check apakah riwayat dapat dihapus
     */
    public function canDeleteRiwayat(PengajuanSurat $pengajuan): bool
    {
        return in_array($pengajuan->status, ['selesai', 'ditolak']);
    }
}