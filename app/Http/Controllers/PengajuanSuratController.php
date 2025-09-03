<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use App\Services\Interfaces\PengajuanSuratServiceInterface;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Requests\StorePengajuanSuratRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Inertia\Response;

class PengajuanSuratController extends Controller
{
    use AuthorizesRequests;

    protected PengajuanSuratServiceInterface $pengajuanService;

    public function __construct(PengajuanSuratServiceInterface $pengajuanService)
    {
        $this->pengajuanService = $pengajuanService;
    }

    /**
     * Menampilkan halaman utama pengajuan surat untuk warga.
     */
    public function index(): Response
    {
        $user = Auth::user();
        $data = $this->pengajuanService->getIndexData($user);

        return Inertia::render('Pengajuan/Index', $data);
    }

    /**
     * Menyimpan pengajuan surat baru.
     */
    public function store(StorePengajuanSuratRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->pengajuanService->store($validated, Auth::id());

            return redirect()
                ->route('pengajuan.index')
                ->with('success', 'Pengajuan surat berhasil dikirim.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Mengunduh file hasil pengajuan.
     */
    public function download(PengajuanSurat $pengajuan): BinaryFileResponse
    {
        $this->authorize('view', $pengajuan);

        try {
            return $this->pengajuanService->downloadFileHasil($pengajuan);
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Method untuk melihat lampiran
     */
    public function viewLampiran(PengajuanSurat $pengajuan, string $key): BinaryFileResponse
    {
        $this->authorize('view', $pengajuan);

        try {
            return $this->pengajuanService->viewLampiran($pengajuan, $key);
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Membatalkan pengajuan yang masih pending.
     */
    public function destroy(PengajuanSurat $pengajuan)
    {
        $this->authorize('delete', $pengajuan);

        try {
            $this->pengajuanService->cancelPengajuan($pengajuan);

            return redirect()
                ->route('pengajuan.index')
                ->with('success', 'Pengajuan berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Menghapus riwayat pengajuan.
     */
    public function destroyRiwayat(PengajuanSurat $pengajuan)
    {
        $this->authorize('delete', $pengajuan);

        try {
            $this->pengajuanService->deleteRiwayat($pengajuan);

            return redirect()
                ->route('pengajuan.index')
                ->with('success', 'Riwayat pengajuan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}