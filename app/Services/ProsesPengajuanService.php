<?php

namespace App\Services;

use App\Models\PengajuanSurat;
use App\Models\ProfilDesa;
use App\Repositories\Interfaces\ProsesPengajuanRepositoryInterface;
use App\Services\Interfaces\ProsesPengajuanServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ProsesPengajuanService implements ProsesPengajuanServiceInterface
{
    protected ProsesPengajuanRepositoryInterface $repository;

    public function __construct(ProsesPengajuanRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Mengambil data untuk halaman index pengajuan
     */
    public function getIndexData(Request $request): array
    {
        $pengajuanList = $this->repository->getPaginatedByStatus(['pending', 'diproses'], $request);
        
        return [
            'pengajuanList' => $pengajuanList,
            'filters' => $request->only(['search', 'status', 'jenis_surat']),
        ];
    }

    /**
     * Mengambil data untuk halaman riwayat pengajuan
     */
    public function getRiwayatData(Request $request): array
    {
        $pengajuanList = $this->repository->getPaginatedByStatus(['selesai', 'ditolak'], $request);
        
        return [
            'pengajuanList' => $pengajuanList,
            'filters' => $request->only(['search', 'status', 'jenis_surat']),
        ];
    }

    /**
     * Memperbarui status pengajuan
     */
   // Method updateStatus yang diperbaiki di ProsesPengajuanService

/**
 * Memperbarui status pengajuan
 */
public function updateStatus(PengajuanSurat $pengajuan, array $data): PengajuanSurat
{
    $validatedData = $this->validateUpdateData($data, $pengajuan);
    
    // Update timeline jika status berubah
    if ($pengajuan->status !== $validatedData['status']) {
        $message = $this->getStatusMessage($validatedData['status']);
        $pengajuan = $this->repository->updateTimeline($pengajuan, $validatedData['status'], $message);
    }

    return $this->repository->update($pengajuan, $validatedData);
}

    /**
     * Generate dan download surat
     */
    public function generateSurat(PengajuanSurat $pengajuan): BinaryFileResponse
    {
        // Validasi status
        if ($pengajuan->status === 'pending') {
            throw new \Exception('Ubah status pengajuan terlebih dahulu sebelum men-generate surat.');
        }

        try {
            // Load data yang diperlukan
            $profilDesa = ProfilDesa::first();
            if (!$profilDesa) {
                throw new \Exception('Data profil desa belum diatur. Silakan lengkapi profil desa terlebih dahulu.');
            }

            // Validasi jenis surat dan template
            $jenisSurat = $pengajuan->jenisSurat;
            if (!$jenisSurat) {
                throw new \Exception('Jenis surat tidak ditemukan.');
            }

            $templatePath = $this->getTemplatePath($jenisSurat->template_surat);
            
            // Buat direktori temp jika belum ada
            $tempDir = storage_path('app/temp');
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            // Proses template
            $templateProcessor = new TemplateProcessor($templatePath);
            $this->fillTemplateData($templateProcessor, $pengajuan, $profilDesa);

            // Generate nama file output
            $namaFile = Str::slug($jenisSurat->nama_surat . '-' . $pengajuan->user->name . '-' . now()->format('Y-m-d'));
            $outputPath = $tempDir . '/' . $namaFile . '.docx';

            // Save processed template
            $templateProcessor->saveAs($outputPath);

            // Verify file was created
            if (!File::exists($outputPath)) {
                throw new \Exception('Gagal membuat file surat. Silakan coba lagi.');
            }

            return response()->download($outputPath, $namaFile . '.docx')
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Gagal generate surat', [
                'pengajuan_id' => $pengajuan->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            throw new \Exception('Terjadi kesalahan sistem saat membuat surat: ' . $e->getMessage());
        }
    }

    /**
     * Upload file oleh admin
     */
  /**
 * Upload file oleh admin - Updated untuk konsistensi
 */
public function uploadFile(Request $request, PengajuanSurat $pengajuan): PengajuanSurat
{
    // Ganti 'file' menjadi 'file_final' agar cocok dengan frontend
    $validator = Validator::make($request->all(), [
        'file_final' => 'required|mimes:pdf,doc,docx|max:2048',
    ], [
        'file_final.required' => 'Anda harus memilih sebuah file untuk diunggah.',
        'file_final.mimes' => 'Format file harus PDF, DOC, atau DOCX.',
        'file_final.max' => 'Ukuran file maksimal adalah 2MB.',
    ]);

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

    try {
        // Hapus file lama jika ada
        if ($pengajuan->file_final && Storage::disk('public')->exists($pengajuan->file_final)) {
            Storage::disk('public')->delete($pengajuan->file_final);
        }
        
        // Upload file baru
        $path = $request->file('file_final')->store('surat_final', 'public');
        
        // Update database
        $updatedPengajuan = $this->repository->updateFileFinal($pengajuan, $path);
        
        return $updatedPengajuan->fresh(); // Refresh data dari database

    } catch (\Exception $e) {
        Log::error('Gagal upload file', [
            'pengajuan_id' => $pengajuan->id,
            'error' => $e->getMessage()
        ]);
        
        throw new \Exception('Terjadi kesalahan saat mengupload file: ' . $e->getMessage());
    }
}
    /**
     * Menghapus file yang diupload admin
     */
    public function hapusFile(PengajuanSurat $pengajuan): bool
    {
        if ($pengajuan->file_final && Storage::disk('public')->exists($pengajuan->file_final)) {
            Storage::disk('public')->delete($pengajuan->file_final);
            $this->repository->updateFileFinal($pengajuan, null);
            return true;
        }
        
        throw new \Exception('File tidak ditemukan.');
    }

   /**
 * Konfirmasi file sebagai final
 */
public function konfirmasiFinal(PengajuanSurat $pengajuan): PengajuanSurat
{
    if (!$pengajuan->file_final) {
        throw new \Exception('Belum ada file untuk dikonfirmasi.');
    }

    try {
        // Hapus file hasil lama jika ada
        if ($pengajuan->file_hasil && Storage::disk('public')->exists($pengajuan->file_hasil)) {
            Storage::disk('public')->delete($pengajuan->file_hasil);
        }

        // Update status menjadi selesai dan pindahkan file_final ke file_hasil
        $updatedPengajuan = $this->repository->confirmFinalFile($pengajuan);
        
        // Update timeline untuk status selesai
        $message = $this->getStatusMessage('selesai');
        $this->repository->updateTimeline($updatedPengajuan, 'selesai', $message);
        
        return $updatedPengajuan->fresh(); // Refresh data dari database

    } catch (\Exception $e) {
        Log::error('Gagal konfirmasi final', [
            'pengajuan_id' => $pengajuan->id,
            'error' => $e->getMessage()
        ]);
        
        throw new \Exception('Terjadi kesalahan saat mengkonfirmasi file: ' . $e->getMessage());
    }
}


    /**
     * Menghapus riwayat pengajuan
     */
    public function deleteRiwayat(PengajuanSurat $pengajuan): bool
    {
        if (!in_array($pengajuan->status, ['selesai', 'ditolak'])) {
            throw new \Exception('Hanya riwayat yang sudah selesai atau ditolak yang bisa dihapus.');
        }

        try {
            // Hapus semua file lampiran
            if ($pengajuan->lampiran && is_array($pengajuan->lampiran)) {
                foreach ($pengajuan->lampiran as $fileData) {
                    if (is_array($fileData) && isset($fileData['path']) && Storage::disk('public')->exists($fileData['path'])) {
                        Storage::disk('public')->delete($fileData['path']);
                    }
                }
            }

            return $this->repository->delete($pengajuan);

        } catch (\Exception $e) {
            Log::error('Gagal menghapus riwayat pengajuan: ' . $e->getMessage());
            throw new \Exception('Terjadi kesalahan saat menghapus riwayat.');
        }
    }

    /**
     * Menampilkan file lampiran
     */
    public function viewLampiran(PengajuanSurat $pengajuan, string $key): Response
    {
        $fileData = $this->repository->getLampiranByKey($pengajuan, $key);

        if (!$fileData) {
            throw new \Exception('Data file tidak valid atau tidak ditemukan.');
        }

        $filePath = $fileData['path'];

        if (!Storage::disk('public')->exists($filePath)) {
            throw new \Exception('File tidak ditemukan di dalam penyimpanan.');
        }

        return response()->file(Storage::disk('public')->path($filePath));
    }

    /**
 * Validasi data untuk update status - DIPERBAIKI
 */
public function validateUpdateData(array $data, PengajuanSurat $pengajuan): array
{
    // Aturan validasi yang lebih fleksibel
    $rules = [
        'status' => 'required|in:pending,diproses,selesai,ditolak',
        'keterangan_admin' => 'nullable|string|max:1000',
        'nomor_surat' => [
            'nullable',
            'string',
            'max:255',
            // Custom unique rule yang mengabaikan record saat ini
            function ($attribute, $value, $fail) use ($pengajuan) {
                if (!empty($value)) {
                    $exists = \App\Models\PengajuanSurat::where('nomor_surat', $value)
                        ->where('id', '!=', $pengajuan->id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Nomor surat sudah digunakan.');
                    }
                }
            }
        ],
    ];

    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

    $validated = $validator->validated();
    
    // Pastikan nilai null/kosong ditangani dengan benar
    $validated['nomor_surat'] = $validated['nomor_surat'] ?: null;
    $validated['keterangan_admin'] = $validated['keterangan_admin'] ?: null;

    return $validated;
}

    /**
     * Mendapatkan path template yang valid
     */
    private function getTemplatePath(string $templateFileName): string
    {
        if (empty($templateFileName)) {
            throw new \Exception('Jenis surat ini tidak memiliki file template yang terhubung.');
        }

        $templatePath = resource_path('templates/docx/' . $templateFileName);
        
        if (!File::exists($templatePath)) {
            // Coba cari file dengan ekstensi berbeda
            $templatePathWithoutExt = resource_path('templates/docx/' . pathinfo($templateFileName, PATHINFO_FILENAME));
            $possibleFiles = [
                $templatePathWithoutExt . '.docx',
                $templatePathWithoutExt . '.DOCX',
                $templatePath,
            ];
            
            $foundTemplate = null;
            foreach ($possibleFiles as $possibleFile) {
                if (File::exists($possibleFile)) {
                    $foundTemplate = $possibleFile;
                    break;
                }
            }
            
            if (!$foundTemplate) {
                Log::error('Template tidak ditemukan', [
                    'searched_paths' => $possibleFiles,
                    'template_dir_exists' => File::exists(resource_path('templates/docx/')),
                    'files_in_dir' => File::exists(resource_path('templates/docx/')) 
                        ? File::files(resource_path('templates/docx/')) 
                        : []
                ]);
                
                throw new \Exception('File template surat (.docx) tidak ditemukan di: ' . $templatePath);
            }
            
            $templatePath = $foundTemplate;
        }

        return $templatePath;
    }

    /**
     * Mengisi data template
     */
    private function fillTemplateData(TemplateProcessor $templateProcessor, PengajuanSurat $pengajuan, ProfilDesa $profilDesa): void
    {
        $tanggal = now()->locale('id_ID')->translatedFormat('d F Y');
        $user = $pengajuan->user;

        // Data Profil Desa
        $templateProcessor->setValue('nama_kabupaten', strtoupper($profilDesa->nama_kabupaten ?? ''));
        $templateProcessor->setValue('nama_kecamatan', strtoupper($profilDesa->nama_kecamatan ?? ''));
        $templateProcessor->setValue('nama_desa', strtoupper($profilDesa->nama_desa ?? ''));
        $templateProcessor->setValue('alamat_desa', $profilDesa->alamat ?? '');
        $templateProcessor->setValue('telepon_desa', $profilDesa->phone_number ?? '-');
        $templateProcessor->setValue('email_desa', $profilDesa->email ?? '-');

        // Proper case variants
        $templateProcessor->setValue('nama_desa_proper', $profilDesa->nama_desa ?? '');
        $templateProcessor->setValue('nama_kecamatan_proper', $profilDesa->nama_kecamatan ?? '');
        $templateProcessor->setValue('nama_kabupaten_proper', $profilDesa->nama_kabupaten ?? '');
        $templateProcessor->setValue('nama_kepala_desa', $profilDesa->nama_kepala_desa ?? '');

        // Data Surat dan Pemohon
        $templateProcessor->setValue('nomor_surat', $pengajuan->nomor_surat ?? '________________');
        $templateProcessor->setValue('tanggal_surat', $tanggal);
        $templateProcessor->setValue('keperluan', $pengajuan->keperluan ?? '-');
        $templateProcessor->setValue('nama_pemohon', $user->name ?? '');
        $templateProcessor->setValue('nik', $user->nik ?? '');
        $templateProcessor->setValue('tempat_lahir', $user->tempat_lahir ?? '');
        
        if ($user->tanggal_lahir) {
            $templateProcessor->setValue('tanggal_lahir', 
                \Carbon\Carbon::parse($user->tanggal_lahir)->locale('id_ID')->translatedFormat('d F Y')
            );
        } else {
            $templateProcessor->setValue('tanggal_lahir', '-');
        }
        
        $templateProcessor->setValue('jenis_kelamin', $user->jenis_kelamin ?? '');
        $templateProcessor->setValue('pekerjaan', $user->pekerjaan ?? '-');
        $templateProcessor->setValue('agama', $user->agama ?? '-');
        $templateProcessor->setValue('status_perkawinan', $user->status_perkawinan ?? '-');
        $templateProcessor->setValue('kewarganegaraan', $user->kewarganegaraan ?? 'Indonesia');
        $templateProcessor->setValue('alamat_pemohon', $user->address ?? '');

        // Handle logo desa
        if ($profilDesa->logo && File::exists(storage_path('app/public/' . $profilDesa->logo))) {
            try {
                $templateProcessor->setImageValue('logo_desa', [
                    'path' => storage_path('app/public/' . $profilDesa->logo), 
                    'width' => 80, 
                    'height' => 80, 
                    'ratio' => true
                ]);
            } catch (\Exception $e) {
                Log::warning('Gagal set logo desa: ' . $e->getMessage());
                $templateProcessor->setValue('logo_desa', '');
            }
        } else {
            $templateProcessor->setValue('logo_desa', '');
        }
    }

    /**
     * Mendapatkan pesan status berdasarkan status baru
     */
    private function getStatusMessage(string $status): string
    {
        return match ($status) {
            'diproses' => 'Pengajuan sedang ditinjau dan diproses oleh admin.',
            'selesai' => 'Pengajuan telah disetujui dan surat selesai dibuat.',
            'ditolak' => 'Pengajuan ditolak.',
            default => 'Status diperbarui menjadi ' . $status . '.',
        };
    }
}