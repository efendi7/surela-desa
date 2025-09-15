<?php

namespace App\Services\Interfaces;

use App\Models\PengajuanSurat;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface PengajuanSuratServiceInterface
{
    /**
     * Get data untuk halaman index pengajuan
     */
    public function getIndexData(User $user): array;

    /**
     * [PERUBAHAN] Method ini dihapus dari kontrak karena logikanya 
     * sudah dipindahkan ke accessor di model User.
     */
    // public function isProfileComplete(User $user): bool;

    /**
     * Process dan tambahkan URL lampiran ke pengajuan
     */
    public function processLampiranUrls(PengajuanSurat $pengajuan): PengajuanSurat;

    /**
     * Process dan tambahkan URL lampiran ke collection pengajuan
     */
    public function processLampiranUrlsCollection(Collection $pengajuanCollection): Collection;

    /**
     * Store pengajuan baru
     */
    public function store(array $validatedData, int $userId): PengajuanSurat;

    /**
     * Upload dan store file lampiran
     */
    public function storeFileLampiran(array $files, int $userId): array;

    /**
     * Download file hasil pengajuan
     */
    public function downloadFileHasil(PengajuanSurat $pengajuan): BinaryFileResponse;

    /**
     * View file lampiran
     */
    public function viewLampiran(PengajuanSurat $pengajuan, string $key): BinaryFileResponse;

    /**
     * Cancel pengajuan
     */
    public function cancelPengajuan(PengajuanSurat $pengajuan): bool;

    /**
     * Delete riwayat pengajuan
     */
    public function deleteRiwayat(PengajuanSurat $pengajuan): bool;

    /**
     * Delete file lampiran dari storage
     */
    public function deleteLampiranFiles(array $lampiran): void;

    /**
     * Delete file hasil dari storage
     */
    public function deleteFileHasil(string $filePath): void;
}