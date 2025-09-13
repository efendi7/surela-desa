<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSurat;
use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use PhpOffice\PhpWord\TemplateProcessor;

class ProsesPengajuanController extends Controller
{
    /**
     * Menampilkan daftar pengajuan yang statusnya 'pending' atau 'diproses'.
     */
   public function index(Request $request)
{
    $query = PengajuanSurat::whereIn('status', ['pending', 'diproses'])
        ->with(['user:id,name,nik,profile_photo_path', 'jenisSurat:id,nama_surat']); // ✅ ambil profile_photo_path juga

    $this->applyFilters($query, $request);

    $perPage = $request->get('per_page', 10);
    $pengajuanList = $query->latest()->paginate($perPage)->withQueryString();

    return Inertia::render('Admin/Pengajuan/Index', [
        'pengajuanList' => $pengajuanList,
        'filters' => $request->only(['search', 'status', 'jenis_surat']),
    ]);
}

    /**
     * Menampilkan riwayat pengajuan yang statusnya 'selesai' atau 'ditolak'.
     */
    public function riwayat(Request $request)
    {
        $query = PengajuanSurat::whereIn('status', ['selesai', 'ditolak'])
           ->with(['user:id,name,nik,profile_photo_path', 'jenisSurat:id,nama_surat']);

            
        $this->applyFilters($query, $request);
            
        $perPage = $request->get('per_page', 10);
        $pengajuanList = $query->latest()->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/Pengajuan/Riwayat', [
            'pengajuanList' => $pengajuanList,
            'filters' => $request->only(['search', 'status', 'jenis_surat']),
        ]);
    }

    /**
     * Mengupdate status, nomor surat, dan keterangan dari sebuah pengajuan.
     */
    public function update(Request $request, PengajuanSurat $pengajuanSurat)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,diproses,selesai,ditolak',
            'keterangan_admin' => 'nullable|string|max:1000',
            'nomor_surat' => 'nullable|string|max:255|unique:pengajuan_surats,nomor_surat,' . $pengajuanSurat->id,
        ]);
        
        $timeline = $pengajuanSurat->timeline ?? [];

        if ($pengajuanSurat->status !== $validated['status']) {
            $newMessage = match ($validated['status']) {
                'diproses' => 'Pengajuan sedang ditinjau dan diproses oleh admin.',
                'selesai' => 'Pengajuan telah disetujui dan surat selesai dibuat.',
                'ditolak' => 'Pengajuan ditolak.',
                default => 'Status diperbarui menjadi ' . $validated['status'] . '.',
            };

            $timeline[] = [
                'status' => $validated['status'],
                'message' => $newMessage,
                'timestamp' => now(),
            ];
        }

        $pengajuanSurat->fill($validated);
        $pengajuanSurat->timeline = $timeline;
        $pengajuanSurat->save();
        
        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    /**
     * Generate dan download surat berdasarkan template .docx yang sudah diisi data.
     */
    
   public function generateSurat(PengajuanSurat $pengajuan)
{
    // 1. Validasi status
    if ($pengajuan->status === 'pending') {
        return back()->with('error', 'Ubah status pengajuan terlebih dahulu sebelum men-generate surat.');
    }

    try {
        // 2. Load data yang diperlukan
        $profilDesa = ProfilDesa::first();
        if (!$profilDesa) {
            return back()->with('error', 'Data profil desa belum diatur. Silakan lengkapi profil desa terlebih dahulu.');
        }

        // 3. Cek jenis surat dan template
        $jenisSurat = $pengajuan->jenisSurat;
        if (!$jenisSurat) {
            return back()->with('error', 'Jenis surat tidak ditemukan.');
        }

        $templateFileName = $jenisSurat->template_surat;
        if (empty($templateFileName)) {
            return back()->with('error', 'Jenis surat ini tidak memiliki file template yang terhubung.');
        }

        // 4. Validasi path template
        $templatePath = resource_path('templates/docx/' . $templateFileName);
        
        // Debug log
        Log::info('Generating surat', [
            'pengajuan_id' => $pengajuan->id,
            'template_file' => $templateFileName,
            'template_path' => $templatePath,
            'file_exists' => File::exists($templatePath)
        ]);

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
                
                return back()->with('error', 'File template surat (.docx) tidak ditemukan di: ' . $templatePath);
            }
            
            $templatePath = $foundTemplate;
        }

        // 5. Buat direktori temp jika belum ada
        $tempDir = storage_path('app/temp');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        // 6. Proses template
        $templateProcessor = new TemplateProcessor($templatePath);
        
        // 7. Data tanggal
        $tanggal = now()->locale('id_ID')->translatedFormat('d F Y');

        // 8. Replace template variables - Profil Desa
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

        // 9. Data Surat dan Pemohon
        $user = $pengajuan->user;
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

        // 10. Handle logo desa
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

        // 11. Generate nama file output
        $namaFile = Str::slug($jenisSurat->nama_surat . '-' . $user->name . '-' . now()->format('Y-m-d'));
        $outputPath = $tempDir . '/' . $namaFile . '.docx';

        // 12. Save processed template
        $templateProcessor->saveAs($outputPath);

        // 13. Verify file was created
        if (!File::exists($outputPath)) {
            Log::error('File output tidak berhasil dibuat: ' . $outputPath);
            return back()->with('error', 'Gagal membuat file surat. Silakan coba lagi.');
        }

        // 14. Return download response
        return response()->download($outputPath, $namaFile . '.docx')
            ->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        Log::error('Gagal generate surat', [
            'pengajuan_id' => $pengajuan->id,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return back()->with('error', 'Terjadi kesalahan sistem saat membuat surat: ' . $e->getMessage());
    }
}


    
    /**
     * Mengunggah file draft/final oleh admin.
     */
    public function uploadFile(Request $request, PengajuanSurat $pengajuan)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($pengajuan->file_final && Storage::disk('public')->exists($pengajuan->file_final)) {
            Storage::disk('public')->delete($pengajuan->file_final);
        }
        
        $path = $request->file('file')->store('surat_final', 'public');

        $pengajuan->update(['file_final' => $path]);

        return back()->with('success', 'File berhasil diupload.');
    }

    /**
     * Menghapus file draft/final yang diupload admin.
     */
    public function hapusFile(PengajuanSurat $pengajuan)
    {
        if ($pengajuan->file_final && Storage::disk('public')->exists($pengajuan->file_final)) {
            Storage::disk('public')->delete($pengajuan->file_final);
            $pengajuan->update(['file_final' => null]);
            return back()->with('success', 'File berhasil dihapus.');
        }
        return back()->with('error', 'File tidak ditemukan.');
    }

    /**
     * Mengonfirmasi file draft menjadi file hasil final dengan timeline tracking.
     */
    public function konfirmasiFinal(Request $request, PengajuanSurat $pengajuan)
    {
        if (!$pengajuan->file_final) {
            return back()->with('error', 'Belum ada file untuk dikonfirmasi.');
        }

        if ($pengajuan->file_hasil && Storage::disk('public')->exists($pengajuan->file_hasil)) {
            Storage::disk('public')->delete($pengajuan->file_hasil);
        }

        $timeline = $pengajuan->timeline ?? [];
        if ($pengajuan->status !== 'selesai') {
             $timeline[] = [
                'status' => 'selesai',
                'message' => 'Pengajuan telah disetujui dan surat selesai dibuat.',
                'timestamp' => now(),
            ];
        }

        $pengajuan->update([
            'file_hasil' => $pengajuan->file_final,
            'status' => 'selesai',
            'file_final' => null,
            'timeline' => $timeline,
        ]);

        return back()->with('success', 'Surat berhasil dikonfirmasi sebagai final.');
    }

    /**
     * Menghapus data riwayat pengajuan beserta semua file lampirannya.
     */
    public function destroy(PengajuanSurat $pengajuan)
    {
        if (!in_array($pengajuan->status, ['selesai', 'ditolak'])) {
            return redirect()->back()->with('error', 'Hanya riwayat yang sudah selesai atau ditolak yang bisa dihapus.');
        }

        try {
            if ($pengajuan->lampiran && is_array($pengajuan->lampiran)) {
                foreach ($pengajuan->lampiran as $fileData) {
                    if (is_array($fileData) && isset($fileData['path']) && Storage::disk('public')->exists($fileData['path'])) {
                        Storage::disk('public')->delete($fileData['path']);
                    }
                }
            }

            $pengajuan->delete();

            return redirect()->back()->with('success', 'Riwayat pengajuan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Gagal menghapus riwayat pengajuan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus riwayat.');
        }
    }

    /**
     * Menampilkan file lampiran yang diunggah oleh warga untuk dilihat oleh admin.
     */
    public function viewLampiran(PengajuanSurat $pengajuan, $key)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        $fileData = $pengajuan->lampiran[$key] ?? null;

        if (!$fileData || !is_array($fileData) || !isset($fileData['path'])) {
            abort(404, 'Data file tidak valid atau tidak ditemukan.');
        }

        $filePath = $fileData['path'];

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan di dalam penyimpanan.');
        }

        return response()->file(Storage::disk('public')->path($filePath));
    }

    /**
     * Menerapkan filter pencarian dan status pada query Eloquent.
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('nomor_surat', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_surat')) {
            $query->whereHas('jenisSurat', function ($jenisQuery) use ($request) {
                $jenisQuery->where('nama_surat', $request->jenis_surat);
            });
        }
    }
}
