<?php

namespace App\Services;

use App\Models\JenisSurat;
use App\Models\PengajuanSurat;
use App\Models\User;
use App\Repositories\Interfaces\PengajuanSuratRepositoryInterface;
use App\Services\Interfaces\PengajuanSuratServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PengajuanSuratService implements PengajuanSuratServiceInterface
{
    protected PengajuanSuratRepositoryInterface $repository;

    public function __construct(PengajuanSuratRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get data untuk halaman index pengajuan
     */
    public function getIndexData(User $user): array
    {
        $pengajuanAktif = $this->processLampiranUrlsCollection(
            $this->repository->getActivePengajuanByUser($user->id)
        );

        $riwayatPengajuan = $this->processLampiranUrlsCollection(
            $this->repository->getRiwayatPengajuanByUser($user->id)
        );

        return [
            'jenisSuratTersedia' => JenisSurat::get(['id', 'nama_surat', 'syarat']),
            'pengajuanAktif' => $pengajuanAktif,
            'riwayatPengajuan' => $riwayatPengajuan,
            'isProfileComplete' => $this->isProfileComplete($user),
        ];
    }

    /**
     * Check apakah profil user sudah lengkap
     */
    public function isProfileComplete(User $user): bool
    {
        $requiredProfileFields = [
            'nik', 'address', 'phone', 'tempat_lahir', 'tanggal_lahir', 
            'jenis_kelamin', 'pekerjaan', 'agama', 'status_perkawinan', 'kewarganegaraan'
        ];

        return collect($requiredProfileFields)->every(fn($field) => !empty($user->{$field}));
    }

    /**
     * Process dan tambahkan URL lampiran ke pengajuan
     */
    public function processLampiranUrls(PengajuanSurat $pengajuan): PengajuanSurat
    {
        if ($pengajuan->lampiran && is_array($pengajuan->lampiran)) {
            $lampiranWithUrls = [];
            foreach ($pengajuan->lampiran as $key => $fileData) {
                if (is_array($fileData) && isset($fileData['path'])) {
                    $lampiranWithUrls[$key] = [
                        'path' => $fileData['path'],
                        'original_name' => $fileData['original_name'] ?? 'Document.pdf',
                        'url' => Storage::disk('public')->url($fileData['path']),
                        'exists' => Storage::disk('public')->exists($fileData['path'])
                    ];
                }
            }
            $pengajuan->lampiran = $lampiranWithUrls;
        }
        
        // Juga tambahkan URL untuk file hasil jika ada
        if ($pengajuan->file_hasil) {
            $pengajuan->file_hasil_url = Storage::disk('public')->url($pengajuan->file_hasil);
            $pengajuan->file_hasil_exists = Storage::disk('public')->exists($pengajuan->file_hasil);
        }
        
        return $pengajuan;
    }

    /**
     * Process dan tambahkan URL lampiran ke collection pengajuan
     */
    public function processLampiranUrlsCollection(Collection $pengajuanCollection): Collection
    {
        return $pengajuanCollection->map(function ($pengajuan) {
            return $this->processLampiranUrls($pengajuan);
        });
    }

    /**
     * Store pengajuan baru
     */
    public function store(array $validatedData, int $userId): PengajuanSurat
    {
        $lampiranPaths = [];
        if (isset($validatedData['lampiran'])) {
            $lampiranPaths = $this->storeFileLampiran($validatedData['lampiran'], $userId);
        }

        // Membuat entri timeline pertama saat pengajuan dibuat
        $initialTimeline = [
            [
                'status' => 'pending',
                'message' => 'Pengajuan dibuat oleh warga.',
                'timestamp' => now()->toISOString(),
            ]
        ];

        $data = [
            'user_id' => $userId,
            'jenis_surat_id' => $validatedData['jenis_surat_id'],
            'status' => 'pending',
            'lampiran' => $lampiranPaths,
            'timeline' => $initialTimeline,
        ];

        return $this->repository->create($data);
    }

    /**
     * Upload dan store file lampiran
     */
    public function storeFileLampiran(array $files, int $userId): array
    {
        $lampiranPaths = [];
        foreach ($files as $key => $file) {
            if ($file instanceof UploadedFile) {
                $path = $file->store('lampiran_warga/' . $userId, 'public');
                $lampiranPaths[$key] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ];
            }
        }
        return $lampiranPaths;
    }

    /**
     * Download file hasil pengajuan - FIXED VERSION
     */
    public function downloadFileHasil(PengajuanSurat $pengajuan): BinaryFileResponse
    {
        if (!$pengajuan->file_hasil || !Storage::disk('public')->exists($pengajuan->file_hasil)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Get file extension from the actual file
        $fileExtension = pathinfo($pengajuan->file_hasil, PATHINFO_EXTENSION);
        $fileName = 'Surat_' . $pengajuan->jenisSurat->nama_surat . '_' . $pengajuan->user->name . '.' . $fileExtension;
        
        // Use response()->download() instead of Storage::download() to ensure BinaryFileResponse
        $fullPath = Storage::disk('public')->path($pengajuan->file_hasil);
        
        return response()->download($fullPath, $fileName);
    }

    /**
     * View file lampiran
     */
    public function viewLampiran(PengajuanSurat $pengajuan, string $key): BinaryFileResponse
    {
        \Log::info('ViewLampiran called', [
            'pengajuan_id' => $pengajuan->id,
            'key' => $key,
            'lampiran' => $pengajuan->lampiran
        ]);

        if (!$pengajuan->lampiran || !isset($pengajuan->lampiran[$key])) {
            \Log::error('File lampiran tidak ditemukan', [
                'pengajuan_id' => $pengajuan->id,
                'key' => $key,
                'available_keys' => $pengajuan->lampiran ? array_keys($pengajuan->lampiran) : []
            ]);
            abort(404, 'File lampiran tidak ditemukan.');
        }

        $fileData = $pengajuan->lampiran[$key];
        
        \Log::info('File data found', ['file_data' => $fileData]);

        if (!is_array($fileData) || !isset($fileData['path'])) {
            \Log::error('Invalid file data structure', ['file_data' => $fileData]);
            abort(404, 'Data file tidak valid.');
        }

        $filePath = $fileData['path'];

        if (!Storage::disk('public')->exists($filePath)) {
            \Log::error('File tidak ditemukan di storage', [
                'path' => $filePath,
                'full_path' => Storage::disk('public')->path($filePath)
            ]);
            abort(404, 'File tidak ditemukan di storage.');
        }

        $fileName = $fileData['original_name'] ?? 'document.pdf';
        
        try {
            $mimeType = Storage::disk('public')->mimeType($filePath);
            
            \Log::info('Serving file', [
                'path' => $filePath,
                'filename' => $fileName,
                'mime_type' => $mimeType
            ]);
            
            return response()->file(
                Storage::disk('public')->path($filePath),
                [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"'
                ]
            );
        } catch (\Exception $e) {
            \Log::error('Error serving file', [
                'error' => $e->getMessage(),
                'path' => $filePath
            ]);
            abort(500, 'Terjadi kesalahan saat membuka file.');
        }
    }

    /**
     * Cancel pengajuan
     */
    public function cancelPengajuan(PengajuanSurat $pengajuan): bool
    {
        if (!$this->repository->canBeCancelled($pengajuan)) {
            throw new \Exception('Pengajuan ini sudah diproses dan tidak bisa dibatalkan.');
        }
        
        // Hapus file lampiran dari storage
        if ($pengajuan->lampiran && is_array($pengajuan->lampiran)) {
            $this->deleteLampiranFiles($pengajuan->lampiran);
        }

        return $this->repository->delete($pengajuan);
    }

    /**
     * Delete riwayat pengajuan
     */
    public function deleteRiwayat(PengajuanSurat $pengajuan): bool
    {
        if (!$this->repository->canDeleteRiwayat($pengajuan)) {
            throw new \Exception('Hanya riwayat yang sudah selesai atau ditolak yang bisa dihapus.');
        }

        // Hapus semua file terkait (lampiran dan hasil)
        if ($pengajuan->lampiran && is_array($pengajuan->lampiran)) {
            $this->deleteLampiranFiles($pengajuan->lampiran);
        }

        if ($pengajuan->file_hasil) {
            $this->deleteFileHasil($pengajuan->file_hasil);
        }

        return $this->repository->delete($pengajuan);
    }

    /**
     * Delete file lampiran dari storage
     */
    public function deleteLampiranFiles(array $lampiran): void
    {
        foreach ($lampiran as $fileData) {
            if (is_array($fileData) && isset($fileData['path'])) {
                Storage::disk('public')->delete($fileData['path']);
            }
        }
    }

    /**
     * Delete file hasil dari storage
     */
    public function deleteFileHasil(string $filePath): void
    {
        Storage::disk('public')->delete($filePath);
    }
}