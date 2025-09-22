<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSurat;
use App\Services\Interfaces\ProsesPengajuanServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ProsesPengajuanController extends Controller
{
    protected ProsesPengajuanServiceInterface $prosesPengajuanService;

    public function __construct(ProsesPengajuanServiceInterface $prosesPengajuanService)
    {
        $this->prosesPengajuanService = $prosesPengajuanService;
    }

    /**
     * Menampilkan daftar pengajuan yang statusnya 'pending' atau 'diproses'.
     */
    public function index(Request $request): InertiaResponse
    {
        $data = $this->prosesPengajuanService->getIndexData($request);

        return Inertia::render('Admin/Pengajuan/Index', $data);
    }

    /**
     * Menampilkan riwayat pengajuan yang statusnya 'selesai' atau 'ditolak'.
     */
    public function riwayat(Request $request): InertiaResponse
    {
        $data = $this->prosesPengajuanService->getRiwayatData($request);

        return Inertia::render('Admin/Pengajuan/Riwayat', $data);
    }

    /**
     * Mengupdate status, nomor surat, dan keterangan dari sebuah pengajuan.
     */
  public function update(Request $request, PengajuanSurat $pengajuanSurat)
{
    try {
        $updatedPengajuan = $this->prosesPengajuanService->updateStatus(
            $pengajuanSurat, 
            $request->all()
        );

        return back()->with('success', 'Pengajuan berhasil diperbarui.');
        
    } catch (ValidationException $e) {
        // Kirim pesan error spesifik untuk nomor surat
        $errors = $e->errors();
        
        if (isset($errors['nomor_surat'])) {
            return back()->with('error', 'Error nomor surat: ' . $errors['nomor_surat'][0]);
        }
        
        if (isset($errors['keterangan_admin'])) {
            return back()->with('error', 'Error keterangan admin: ' . $errors['keterangan_admin'][0]);
        }
        
        // Error umum jika tidak ada error spesifik
        return back()->with('error', 'Data tidak valid. Silakan periksa kembali inputan Anda.');
        
    } catch (\Exception $e) {
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    /**
     * Generate dan download surat berdasarkan template .docx yang sudah diisi data.
     */
    public function generateSurat(PengajuanSurat $pengajuan): BinaryFileResponse|RedirectResponse
    {
        try {
            return $this->prosesPengajuanService->generateSurat($pengajuan);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mengunggah file draft/final oleh admin.
     */
  public function uploadFile(Request $request, PengajuanSurat $pengajuan)
{
    try {
        $this->prosesPengajuanService->uploadFile($request, $pengajuan);

        // ✅ penting: redirect, bukan cuma back()
        return redirect()->back()->with('success', 'File berhasil diupload');

        // atau kalau mau langsung ke index
        // return redirect()->route('admin.proses.index')->with('success', 'File berhasil diupload');

    } catch (ValidationException $e) {
        return redirect()->back()->withErrors($e->validator->errors());

    } catch (\Exception $e) {
        Log::error('Upload file gagal: ' . $e->getMessage());

        return redirect()->back()->withErrors([
            'file' => 'Terjadi kesalahan saat mengupload file: ' . $e->getMessage()
        ]);
    }
}



    /**
     * Menghapus file draft/final yang diupload admin.
     */
 public function hapusFile(PengajuanSurat $pengajuan)
{
    try {
        $this->prosesPengajuanService->hapusFile($pengajuan);
        
        // Return redirect back untuk Inertia auto-refresh
        return back()->with('success', 'File berhasil dihapus');
        
    } catch (\Exception $e) {
        Log::error('Hapus file gagal: ' . $e->getMessage());
        
        return back()->withErrors(['hapus' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

    /**
     * Mengonfirmasi file draft menjadi file hasil final dengan timeline tracking.
     */
 public function konfirmasiFinal(Request $request, PengajuanSurat $pengajuan)
{
    try {
        $this->prosesPengajuanService->konfirmasiFinal($pengajuan);

        // Return redirect back untuk Inertia auto-refresh
        return redirect()->back()->with('success', 'File berhasil dikonfirmasi sebagai final');

    } catch (\Exception $e) {
        Log::error('Konfirmasi final gagal: ' . $e->getMessage());

        return redirect()->back()->withErrors([
            'konfirmasi' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}


    /**
     * Menghapus data riwayat pengajuan beserta semua file lampirannya.
     */
    public function destroy(PengajuanSurat $pengajuan): RedirectResponse
    {
        try {
            $this->prosesPengajuanService->deleteRiwayat($pengajuan);
            
            return redirect()->back()->with('success', 'Riwayat pengajuan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Menampilkan file lampiran yang diunggah oleh warga untuk dilihat oleh admin.
     */
    public function viewLampiran(PengajuanSurat $pengajuan, string $key): Response
    {
        // Check authorization
        if (!auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }

        try {
            return $this->prosesPengajuanService->viewLampiran($pengajuan, $key);
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }
}