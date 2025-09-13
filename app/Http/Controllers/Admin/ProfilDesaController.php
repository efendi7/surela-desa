<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ProfilDesaServiceInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ProfilDesaController extends Controller
{
    protected ProfilDesaServiceInterface $profilDesaService;

    public function __construct(ProfilDesaServiceInterface $profilDesaService)
    {
        $this->profilDesaService = $profilDesaService;
    }

    public function index(): Response
    {
        try {
            $profilDesa = $this->profilDesaService->getProfilDesa();

            return Inertia::render('Admin/ProfilDesa/Index', [
                'profilDesa' => $profilDesa,
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Admin/ProfilDesa/Index', [
                'error' => 'Gagal memuat profil desa: ' . $e->getMessage(),
                'profilDesa' => null,
            ]);
        }
    }

    public function update(Request $request): RedirectResponse
    {
        try {
            // Perbarui profil
            $this->profilDesaService->updateProfil($request);

            // Redirect + refresh data
            return redirect()
                ->route('admin.profil-desa.index')
                ->with('success', 'Profil desa berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.profil-desa.index')
                ->with('error', 'Gagal memperbarui profil desa: ' . $e->getMessage());
        }
    }
}
