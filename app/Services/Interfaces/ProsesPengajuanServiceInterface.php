<?php

namespace App\Services\Interfaces;

use App\Models\PengajuanSurat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

interface ProsesPengajuanServiceInterface
{
    /**
     * Mengambil data untuk halaman index pengajuan
     *
     * @param Request $request
     * @return array
     */
    public function getIndexData(Request $request): array;

    /**
     * Mengambil data untuk halaman riwayat pengajuan
     *
     * @param Request $request
     * @return array
     */
    public function getRiwayatData(Request $request): array;

    /**
     * Memperbarui status pengajuan
     *
     * @param PengajuanSurat $pengajuan
     * @param array $data
     * @return PengajuanSurat
     * @throws \Exception
     */
    public function updateStatus(PengajuanSurat $pengajuan, array $data): PengajuanSurat;

    /**
     * Generate dan download surat
     *
     * @param PengajuanSurat $pengajuan
     * @return BinaryFileResponse
     * @throws \Exception
     */
    public function generateSurat(PengajuanSurat $pengajuan): BinaryFileResponse;

    /**
     * Upload file oleh admin
     *
     * @param Request $request
     * @param PengajuanSurat $pengajuan
     * @return PengajuanSurat
     * @throws \Exception
     */
    public function uploadFile(Request $request, PengajuanSurat $pengajuan): PengajuanSurat;

    /**
     * Menghapus file yang diupload admin
     *
     * @param PengajuanSurat $pengajuan
     * @return bool
     * @throws \Exception
     */
    public function hapusFile(PengajuanSurat $pengajuan): bool;

    /**
     * Konfirmasi file sebagai final
     *
     * @param PengajuanSurat $pengajuan
     * @return PengajuanSurat
     * @throws \Exception
     */
    public function konfirmasiFinal(PengajuanSurat $pengajuan): PengajuanSurat;

    /**
     * Menghapus riwayat pengajuan
     *
     * @param PengajuanSurat $pengajuan
     * @return bool
     * @throws \Exception
     */
    public function deleteRiwayat(PengajuanSurat $pengajuan): bool;

    /**
     * Menampilkan file lampiran
     *
     * @param PengajuanSurat $pengajuan
     * @param string $key
     * @return Response
     * @throws \Exception
     */
    public function viewLampiran(PengajuanSurat $pengajuan, string $key): Response;

    /**
     * Validasi data untuk update status
     *
     * @param array $data
     * @param PengajuanSurat $pengajuan
     * @return array
     */
    public function validateUpdateData(array $data, PengajuanSurat $pengajuan): array;
}