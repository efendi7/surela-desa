<?php

namespace App\Services;

use App\Models\JenisSurat;
use App\Repositories\Interfaces\JenisSuratRepositoryInterface;
use App\Services\Interfaces\JenisSuratServiceInterface;
use App\Services\Interfaces\TemplateServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class JenisSuratService implements JenisSuratServiceInterface
{
    protected JenisSuratRepositoryInterface $repository;
    protected TemplateServiceInterface $templateService;

    public function __construct(
        JenisSuratRepositoryInterface $repository,
        TemplateServiceInterface $templateService
    ) {
        $this->repository = $repository;
        $this->templateService = $templateService;
    }

    /**
     * Mengambil data untuk halaman index
     *
     * @return array
     */
    public function getIndexData(): array
    {
        return [
            'jenisSurat' => $this->repository->getAllWithActivePengajuanCount(),
            'templateOptions' => $this->templateService->getAvailableTemplates(),
        ];
    }

    /**
     * Membuat jenis surat baru
     *
     * @param array $data
     * @return JenisSurat
     * @throws \Exception
     */
    public function create(array $data): JenisSurat
    {
        $validatedData = $this->validateData($data);
        
        // Validasi template exists
        if (!$this->templateService->templateExists($validatedData['template_surat'])) {
            throw new \Exception('Template surat yang dipilih tidak ditemukan.');
        }

        return $this->repository->create($validatedData);
    }

    /**
     * Memperbarui jenis surat
     *
     * @param JenisSurat $jenisSurat
     * @param array $data
     * @return JenisSurat
     * @throws \Exception
     */
    public function update(JenisSurat $jenisSurat, array $data): JenisSurat
    {
        $validatedData = $this->validateData($data, $jenisSurat);
        
        // Validasi template exists
        if (!$this->templateService->templateExists($validatedData['template_surat'])) {
            throw new \Exception('Template surat yang dipilih tidak ditemukan.');
        }

        return $this->repository->update($jenisSurat, $validatedData);
    }

    /**
     * Menghapus jenis surat
     *
     * @param JenisSurat $jenisSurat
     * @return bool
     * @throws \Exception
     */
    public function delete(JenisSurat $jenisSurat): bool
    {
        $pengajuanAktifCount = $this->repository->countActivePengajuan($jenisSurat);
        
        if ($pengajuanAktifCount > 0) {
            throw new \Exception(
                "Gagal menghapus! Masih ada {$pengajuanAktifCount} pengajuan aktif yang menggunakan jenis surat ini."
            );
        }

        return $this->repository->delete($jenisSurat);
    }

    /**
     * Memvalidasi data jenis surat
     *
     * @param array $data
     * @param JenisSurat|null $jenisSurat
     * @return array
     */
    public function validateData(array $data, ?JenisSurat $jenisSurat = null): array
    {
        $rules = [
            'nama_surat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'template_surat' => 'required|string|max:255',
            'syarat' => 'nullable|array',
        ];

        // Add unique rule for nama_surat
        if ($jenisSurat) {
            $rules['nama_surat'] .= '|unique:jenis_surats,nama_surat,' . $jenisSurat->id;
        } else {
            $rules['nama_surat'] .= '|unique:jenis_surats';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}