<?php

namespace App\Repositories\Interfaces;

use App\Models\PengajuanSurat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface ProsesPengajuanRepositoryInterface
{
    /**
     * Mengambil pengajuan berdasarkan status dengan pagination
     *
     * @param array $statuses
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getPaginatedByStatus(array $statuses, Request $request): LengthAwarePaginator;

    /**
     * Memperbarui pengajuan surat
     *
     * @param PengajuanSurat $pengajuan
     * @param array $data
     * @return PengajuanSurat
     */
    public function update(PengajuanSurat $pengajuan, array $data): PengajuanSurat;

    /**
     * Menghapus pengajuan surat
     *
     * @param PengajuanSurat $pengajuan
     * @return bool
     */
    public function delete(PengajuanSurat $pengajuan): bool;

    /**
     * Mencari pengajuan berdasarkan ID dengan relasi
     *
     * @param int $id
     * @return PengajuanSurat|null
     */
    public function findByIdWithRelations(int $id): ?PengajuanSurat;

    /**
     * Memperbarui timeline pengajuan
     *
     * @param PengajuanSurat $pengajuan
     * @param string $status
     * @param string $message
     * @return PengajuanSurat
     */
    public function updateTimeline(PengajuanSurat $pengajuan, string $status, string $message): PengajuanSurat;

    /**
     * Memperbarui file final
     *
     * @param PengajuanSurat $pengajuan
     * @param string|null $filePath
     * @return PengajuanSurat
     */
    public function updateFileFinal(PengajuanSurat $pengajuan, ?string $filePath): PengajuanSurat;

    /**
     * Konfirmasi file final sebagai hasil akhir
     *
     * @param PengajuanSurat $pengajuan
     * @return PengajuanSurat
     */
    public function confirmFinalFile(PengajuanSurat $pengajuan): PengajuanSurat;

    /**
     * Mengambil data lampiran berdasarkan key
     *
     * @param PengajuanSurat $pengajuan
     * @param string $key
     * @return array|null
     */
    public function getLampiranByKey(PengajuanSurat $pengajuan, string $key): ?array;
}