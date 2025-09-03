<?php

namespace App\Services\Interfaces;

use App\Models\JenisSurat;
use Illuminate\Support\Collection;

interface JenisSuratServiceInterface
{
    /**
     * Mengambil data untuk halaman index
     *
     * @return array
     */
    public function getIndexData(): array;

    /**
     * Membuat jenis surat baru
     *
     * @param array $data
     * @return JenisSurat
     * @throws \Exception
     */
    public function create(array $data): JenisSurat;

    /**
     * Memperbarui jenis surat
     *
     * @param JenisSurat $jenisSurat
     * @param array $data
     * @return JenisSurat
     * @throws \Exception
     */
    public function update(JenisSurat $jenisSurat, array $data): JenisSurat;

    /**
     * Menghapus jenis surat
     *
     * @param JenisSurat $jenisSurat
     * @return bool
     * @throws \Exception
     */
    public function delete(JenisSurat $jenisSurat): bool;

    /**
     * Memvalidasi data jenis surat
     *
     * @param array $data
     * @param JenisSurat|null $jenisSurat
     * @return array
     */
    public function validateData(array $data, ?JenisSurat $jenisSurat = null): array;
}