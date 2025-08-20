<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSurat;
use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class ProsesPengajuanController extends Controller
{
     public function index(Request $request)
    {
        // =================================================================
        // PERUBAHAN: Query hanya untuk status 'pending' dan 'diproses'
        $query = PengajuanSurat::select([
                'id', 'user_id', 'jenis_surat_id', 'status', 'lampiran', 'nomor_surat', 
                'keterangan_admin', 'file_final', 'file_hasil', 'created_at'
            ])
            ->whereIn('status', ['pending', 'diproses'])
            ->with(['user:id,name,nik', 'jenisSurat:id,nama_surat']);
        // =================================================================

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('nomor_surat', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter status di sisi server tidak lagi terlalu relevan di sini, 
        // tapi bisa dipertahankan jika Anda ingin filter antara 'pending' dan 'diproses'.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_surat')) {
            $query->whereHas('jenisSurat', function ($jenisQuery) use ($request) {
                $jenisQuery->where('nama_surat', $request->jenis_surat);
            });
        }
        
        $query->orderBy('created_at', 'desc');
        
        $perPage = $request->get('per_page', 10);
        $pengajuanList = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/Pengajuan/Index', [
            'pengajuanList' => $pengajuanList,
            'filters' => $request->only(['search', 'status', 'jenis_surat']),
        ]);
    }
    public function riwayat(Request $request)
    {
        // =================================================================
        // Query hanya untuk status 'selesai' dan 'ditolak'
        $query = PengajuanSurat::select([
                'id', 'user_id', 'jenis_surat_id', 'status', 'lampiran', 'nomor_surat', 
                'keterangan_admin', 'file_final', 'file_hasil', 'created_at'
            ])
            ->whereIn('status', ['selesai', 'ditolak'])
            ->with(['user:id,name,nik', 'jenisSurat:id,nama_surat']);

        // =================================================================
        
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
        
        $query->orderBy('created_at', 'desc');
        
        $perPage = $request->get('per_page', 10);
        $pengajuanList = $query->paginate($perPage)->withQueryString();

        // Mengarahkan ke komponen Vue 'Riwayat.vue'
        return Inertia::render('Admin/Pengajuan/Riwayat', [
            'pengajuanList' => $pengajuanList,
            'filters' => $request->only(['search', 'status', 'jenis_surat']),
        ]);
    }

    public function update(Request $request, PengajuanSurat $pengajuanSurat)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,selesai,ditolak',
            'keterangan_admin' => 'nullable|string|max:1000',
            'nomor_surat' => 'nullable|string|max:255|unique:pengajuan_surats,nomor_surat,' . $pengajuanSurat->id,
        ]);
        
        if ($request->hasFile('file_hasil') || $request->hasFile('file_final')) {
            return redirect()->back()->with('error', 'Gunakan fitur upload untuk mengubah file.');
        }

        $pengajuanSurat->update($request->only('status', 'keterangan_admin', 'nomor_surat'));
        
        return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }

    public function downloadTemplate(PengajuanSurat $pengajuan)
    {
        if (!in_array($pengajuan->status, ['diproses', 'selesai', 'ditolak'])) {
            return redirect()->back()->with('error', 'Template hanya bisa diunduh jika status Diproses, Selesai, atau Ditolak.');
        }

        try {
            $profilDesa = ProfilDesa::firstOrFail();
            $tanggal = now()->locale('id_ID')->translatedFormat('d F Y');
            $namaFile = Str::slug($pengajuan->jenisSurat->nama_surat . '-' . $pengajuan->user->name . '-template');

            $templatePath = resource_path('templates/docx/' . $pengajuan->jenisSurat->template_surat);
            
            if (!File::exists($templatePath)) {
                return redirect()->back()->with('error', 'Template DOCX tidak ditemukan.');
            }

            $templateProcessor = new TemplateProcessor($templatePath);

            // Set nilai-nilai template
            $templateProcessor->setValue('nama_kabupaten', strtoupper($profilDesa->nama_kabupaten));
            $templateProcessor->setValue('nama_kecamatan', strtoupper($profilDesa->nama_kecamatan));
            $templateProcessor->setValue('nama_desa', strtoupper($profilDesa->nama_desa));
            $templateProcessor->setValue('nama_desa_proper', $profilDesa->nama_desa); 
            $templateProcessor->setValue('nama_kecamatan_proper', $profilDesa->nama_kecamatan); 
            $templateProcessor->setValue('nama_kabupaten_proper', $profilDesa->nama_kabupaten); 
            $templateProcessor->setValue('alamat_desa', $profilDesa->alamat);
            $templateProcessor->setValue('nama_kepala_desa', $profilDesa->nama_kepala_desa);
            $templateProcessor->setValue('nomor_surat', $pengajuan->nomor_surat);
            $templateProcessor->setValue('tanggal_surat', $tanggal);
            $templateProcessor->setValue('nama_pemohon', $pengajuan->user->name);
            $templateProcessor->setValue('tempat_lahir', $pengajuan->user->tempat_lahir);
            $templateProcessor->setValue('tanggal_lahir', \Carbon\Carbon::parse($pengajuan->user->tanggal_lahir)->locale('id_ID')->translatedFormat('d F Y'));
            $templateProcessor->setValue('jenis_kelamin', $pengajuan->user->jenis_kelamin);
            $templateProcessor->setValue('pekerjaan', $pengajuan->user->pekerjaan ?? '-');
            $templateProcessor->setValue('agama', $pengajuan->user->agama ?? '-');
            $templateProcessor->setValue('status_perkawinan', $pengajuan->user->status_perkawinan ?? '-');
            $templateProcessor->setValue('kewarganegaraan', $pengajuan->user->kewarganegaraan ?? 'Indonesia');
            $templateProcessor->setValue('alamat_pemohon', $pengajuan->user->address);
            $templateProcessor->setValue('keperluan', $pengajuan->keperluan ?? '-');

            if ($profilDesa->logo && File::exists(storage_path('app/public/' . $profilDesa->logo))) {
                $templateProcessor->setImageValue('logo_desa', [
                    'path' => storage_path('app/public/' . $profilDesa->logo), 
                    'width' => 80, 
                    'height' => 80, 
                    'ratio' => true
                ]);
            } else {
                $templateProcessor->setValue('logo_desa', '[LOGO]');
            }

            $tempDir = storage_path('app/temp');
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            $outputPath = $tempDir . '/' . $namaFile . '.docx';
            $templateProcessor->saveAs($outputPath);

            if (!File::exists($outputPath)) {
                throw new \Exception('File DOCX tidak berhasil dibuat');
            }

            return response()->download($outputPath, $namaFile . '.docx')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Error saat generate template surat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat template: ' . $e->getMessage());
        }
    }

    public function downloadSuratFinal(PengajuanSurat $pengajuan)
    {
        if ($pengajuan->status !== 'selesai' || !$pengajuan->file_hasil) {
            abort(404, 'File tidak ditemukan atau surat belum selesai diproses.');
        }

        if (!Storage::disk('public')->exists($pengajuan->file_hasil)) {
             abort(404, 'File tidak ditemukan di dalam penyimpanan.');
        }

        $path = Storage::disk('public')->path($pengajuan->file_hasil);
        $namaFileDownload = Str::slug($pengajuan->jenisSurat->nama_surat . '-' . $pengajuan->user->name) . '.' . pathinfo($path, PATHINFO_EXTENSION);
        
        return response()->download($path, $namaFileDownload);
    }

    public function uploadFile(Request $request, PengajuanSurat $pengajuan)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        // hapus file lama jika ada
        if ($pengajuan->file_final && Storage::disk('public')->exists($pengajuan->file_final)) {
            // PERBAIKAN: Gunakan disk public saat menghapus file lama
            Storage::disk('public')->delete($pengajuan->file_final);
        }
        
        // =================================================================
        // UBAH BARIS INI: Tambahkan argumen 'public'
        // Ini akan menyimpan file di storage/app/public/surat_final
        $path = $request->file('file')->store('surat_final', 'public');
        // =================================================================

        $pengajuan->update([
            'file_final' => $path,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'file_final' => $path,
            ]);
        }

        return back()->with('success', 'File berhasil diupload.');
    }

    public function hapusFile(PengajuanSurat $pengajuan)
    {
        // PERBAIKAN: Gunakan disk public saat memeriksa dan menghapus file
        if ($pengajuan->file_final && Storage::disk('public')->exists($pengajuan->file_final)) {
            Storage::disk('public')->delete($pengajuan->file_final);
        }

        $pengajuan->update(['file_final' => null]);

        return back()->with('success', 'File berhasil dihapus.');
    }

    public function konfirmasiFinal(Request $request, PengajuanSurat $pengajuan)
    {
        if (!$pengajuan->file_final) {
            return back()->with('error', 'Belum ada file untuk dikonfirmasi.');
        }

        // hapus file_hasil lama kalau ada
        if ($pengajuan->file_hasil && Storage::disk('public')->exists($pengajuan->file_hasil)) {
            Storage::disk('public')->delete($pengajuan->file_hasil);
        }

        $pengajuan->update([
            'file_hasil' => $pengajuan->file_final,
            'status' => 'selesai',
            'file_final' => null, // Opsional: kosongkan file sementara setelah dikonfirmasi
        ]);

        return back()->with('success', 'Surat berhasil dikonfirmasi sebagai final.');
    }
    public function destroy(PengajuanSurat $pengajuan)
{
    // Pastikan hanya pengajuan yang sudah di arsip yang bisa dihapus
    if (!in_array($pengajuan->status, ['selesai', 'ditolak'])) {
        return redirect()->back()->with('error', 'Hanya riwayat yang sudah selesai atau ditolak yang bisa dihapus.');
    }

    try {
        // Hapus file hasil jika ada di storage
        if ($pengajuan->file_hasil && Storage::disk('public')->exists($pengajuan->file_hasil)) {
            Storage::disk('public')->delete($pengajuan->file_hasil);
        }

        // Hapus record dari database
        $pengajuan->delete();

        return redirect()->back()->with('success', 'Riwayat pengajuan berhasil dihapus.');

    } catch (\Exception $e) {
        Log::error('Gagal menghapus riwayat pengajuan: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus riwayat.');
    }
}
}