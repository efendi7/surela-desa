<?php

namespace App\Services;

use App\Models\PerangkatDesa;
use App\Repositories\Interfaces\PerangkatDesaRepositoryInterface;
use App\Services\Interfaces\PerangkatDesaServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;

class PerangkatDesaService implements PerangkatDesaServiceInterface
{
    protected PerangkatDesaRepositoryInterface $perangkatDesaRepository;

    public function __construct(PerangkatDesaRepositoryInterface $perangkatDesaRepository)
    {
        $this->perangkatDesaRepository = $perangkatDesaRepository;
    }

    // ========== EXISTING ADMIN METHODS ==========

    public function getAllData(Request $request)
    {
        return [
            'perangkatDesa' => $this->perangkatDesaRepository->getAllPaginated(10),
            'jabatanOptions' => $this->perangkatDesaRepository->getJabatanOptions(),
        ];
    }

    public function createPerangkat(Request $request): PerangkatDesa
    {
        $validated = $this->validateRequest($request);
        
        // Handle foto upload
        if ($request->hasFile('foto')) {
            $validated['foto'] = $this->handleFileUpload(
                $request->file('foto'), 
                'foto-perangkat'
            );
        }
        
        // Handle dokumen upload (multiple files)
        if ($request->hasFile('dokumen')) {
            $validated['dokumen'] = $this->handleMultipleFileUpload(
                $request->file('dokumen'), 
                'dokumen-perangkat'
            );
        }
        
        // Asumsi profil_desa_id selalu 1
        $validated['profil_desa_id'] = 1;

        return $this->perangkatDesaRepository->create($validated);
    }

    public function updatePerangkat(Request $request, PerangkatDesa $perangkatDesa): PerangkatDesa
    {
        \Log::info('Update Request Data: ', $request->all());
        \Log::info('File foto exists: ', ['hasFile' => $request->hasFile('foto')]);
        
        // Gunakan validation khusus untuk update
        $validated = $this->validateRequestForUpdate($request, $perangkatDesa->id);
        
        // Handle foto upload
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            \Log::info('Uploading new foto: ', ['filename' => $request->file('foto')->getClientOriginalName()]);
            
            $validated['foto'] = $this->handleFileUpload(
                $request->file('foto'), 
                'foto-perangkat',
                $perangkatDesa->foto
            );
        }
        
        // Handle dokumen upload (multiple files)
        if ($request->hasFile('dokumen')) {
            // Delete old documents first
            $this->deleteOldDocuments($perangkatDesa->dokumen);
            
            $validated['dokumen'] = $this->handleMultipleFileUpload(
                $request->file('dokumen'), 
                'dokumen-perangkat'
            );
        }

        \Log::info('Validated data before update: ', $validated);
        return $this->perangkatDesaRepository->update($perangkatDesa, $validated);
    }

    public function deletePerangkat(PerangkatDesa $perangkatDesa): void
    {
        // Delete foto
        $this->deleteSingleFile($perangkatDesa->foto);
        
        // Delete dokumen
        $this->deleteOldDocuments($perangkatDesa->dokumen);
        
        $this->perangkatDesaRepository->delete($perangkatDesa);
    }

    // ========== NEW PUBLIC METHODS ==========

    /**
     * Get active perangkat desa for public display
     */
    public function getActivePerangkatForPublic(): Collection
    {
        return $this->perangkatDesaRepository->getActiveSortedByJabatan();
    }

    /**
     * Get structured data for public display
     */
    public function getStructuredDataForPublic(): array
    {
        $perangkatAktif = $this->getActivePerangkatForPublic();

        return $perangkatAktif->map(function ($perangkat) {
            return [
                'jabatan' => $perangkat->jabatanDesa->nama_jabatan ?? 'Belum diset',
                'nama' => $perangkat->nama ?? 'Belum diisi',
                'foto' => $perangkat->foto,
                'foto_url' => $perangkat->foto_url,
                'telepon' => $perangkat->telepon,
                'email' => $perangkat->email,
            ];
        })->toArray();
    }

    /**
     * Get perangkat desa by jabatan for specific needs
     */
    public function getPerangkatByJabatan(string $namaJabatan): ?PerangkatDesa
    {
        return $this->perangkatDesaRepository->getActiveWithJabatan()
            ->where('jabatanDesa.nama_jabatan', $namaJabatan)
            ->where('status_jabatan', 'aktif')
            ->first();
    }

    /**
     * Get kepala desa data
     */
    public function getKepalaDesa(): ?PerangkatDesa
    {
        return $this->getPerangkatByJabatan('Kepala Desa');
    }

    // ========== HELPER METHODS ==========

    /**
     * Handle single file upload
     */
    private function handleFileUpload($file, string $directory, ?string $oldFile = null): string
    {
        // Delete old file if exists
        if ($oldFile) {
            $this->deleteSingleFile($oldFile);
        }

        return $file->store($directory, 'public');
    }

    /**
     * Handle multiple file upload
     */
    private function handleMultipleFileUpload(array $files, string $directory): string
    {
        $paths = [];
        foreach ($files as $file) {
            $paths[] = $file->store($directory, 'public');
        }
        return json_encode($paths);
    }

    /**
     * Delete single file
     */
    private function deleteSingleFile(?string $filePath): void
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    /**
     * Delete old documents
     */
    private function deleteOldDocuments(?string $dokumenJson): void
    {
        if (!$dokumenJson) return;

        $dokumen = json_decode($dokumenJson, true);
        if (is_array($dokumen)) {
            foreach ($dokumen as $file) {
                $this->deleteSingleFile($file);
            }
        }
    }

    // ========== VALIDATION METHODS ==========

    // Validation untuk create (semua field required harus ada)
    private function validateRequest(Request $request, $ignoreId = null): array
    {
        $rules = [
            // Data Pribadi
            'nama' => 'required|string|max:255',
            'nik' => [
                'nullable',
                'string',
                'digits:16',
                Rule::unique('perangkat_desa')->ignore($ignoreId)
            ],
            'nip' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('perangkat_desa')->ignore($ignoreId)
            ],
            'jenis_kelamin' => 'nullable|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            
            // Kontak
            'telepon' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('perangkat_desa')->ignore($ignoreId)
            ],
            
            // Jabatan & Kepegawaian
            'jabatan_desa_id' => 'required|exists:jabatan_desas,id',
            'status_kepegawaian' => 'required|in:PNS,PPPK,Honorer,Kontrak',
            'pangkat_golongan' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|in:SD,SMP,SMA/SMK,D1,D2,D3,S1,S2,S3',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status_jabatan' => 'required|in:aktif,non_aktif,cuti,mutasi',
            
            // File & Catatan
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'dokumen' => 'nullable|array',
            'dokumen.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'catatan' => 'nullable|string',
        ];

        return $request->validate($rules);
    }

    // Validation khusus untuk update (hanya validate field yang dikirim)
    private function validateRequestForUpdate(Request $request, $ignoreId): array
    {
        $rules = [
            // Data Pribadi
            'nama' => 'sometimes|required|string|max:255',
            'nik' => [
                'sometimes',
                'nullable',
                'string',
                'digits:16',
                Rule::unique('perangkat_desa')->ignore($ignoreId)
            ],
            'nip' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('perangkat_desa')->ignore($ignoreId)
            ],
            'jenis_kelamin' => 'sometimes|nullable|in:L,P',
            'tempat_lahir' => 'sometimes|nullable|string|max:255',
            'tanggal_lahir' => 'sometimes|nullable|date',
            'alamat' => 'sometimes|nullable|string',
            
            // Kontak
            'telepon' => 'sometimes|nullable|string|max:20',
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
                Rule::unique('perangkat_desa')->ignore($ignoreId)
            ],
            
            // Jabatan & Kepegawaian
            'jabatan_desa_id' => 'sometimes|required|exists:jabatan_desas,id',
            'status_kepegawaian' => 'sometimes|required|in:PNS,PPPK,Honorer,Kontrak',
            'pangkat_golongan' => 'sometimes|nullable|string|max:255',
            'pendidikan_terakhir' => 'sometimes|nullable|in:SD,SMP,SMA/SMK,D1,D2,D3,S1,S2,S3',
            'tanggal_mulai' => 'sometimes|required|date',
            'tanggal_selesai' => 'sometimes|nullable|date|after_or_equal:tanggal_mulai',
            'status_jabatan' => 'sometimes|required|in:aktif,non_aktif,cuti,mutasi',
            
            // File & Catatan
            'foto' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'dokumen' => 'sometimes|nullable|array',
            'dokumen.*' => 'sometimes|nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'catatan' => 'sometimes|nullable|string',
        ];

        // Hanya validate field yang ada di request
        $fieldsToValidate = array_intersect_key($rules, $request->all());
        
        return $request->validate($fieldsToValidate);
    }
}