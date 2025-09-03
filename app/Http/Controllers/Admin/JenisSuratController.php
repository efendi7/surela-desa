<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use App\Services\Interfaces\JenisSuratServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class JenisSuratController extends Controller
{
    protected JenisSuratServiceInterface $jenisSuratService;

    public function __construct(JenisSuratServiceInterface $jenisSuratService)
    {
        $this->jenisSuratService = $jenisSuratService;
    }

    /**
     * Menampilkan halaman manajemen jenis surat.
     * Fungsi ini juga akan memindai folder template .docx dan mengirimkannya ke frontend.
     */
    public function index(): Response
    {
        $data = $this->jenisSuratService->getIndexData();

        return Inertia::render('Admin/JenisSurat/Index', $data);
    }

    /**
     * Menyimpan jenis surat baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $this->jenisSuratService->create($request->all());
            return redirect()
                ->route('admin.jenis-surat.index')
                ->with('success', 'Jenis surat berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Memperbarui data jenis surat yang ada.
     */
    public function update(Request $request, JenisSurat $jenisSurat): RedirectResponse
    {
        try {
            $this->jenisSuratService->update($jenisSurat, $request->all());
            return redirect()
                ->route('admin.jenis-surat.index')
                ->with('success', 'Jenis surat berhasil diperbarui.');
        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Menghapus jenis surat dari database.
     */
    public function destroy(JenisSurat $jenisSurat): RedirectResponse
    {
        try {
            $this->jenisSuratService->delete($jenisSurat);
            return redirect()
                ->route('admin.jenis-surat.index')
                ->with('success', 'Jenis surat berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.jenis-surat.index')
                ->with('error', $e->getMessage());
        }
    }
}