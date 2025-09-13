<?php

namespace App\Services;

use App\Repositories\Interfaces\ProfilDesaRepositoryInterface;
use App\Repositories\Interfaces\PerangkatDesaRepositoryInterface;
use App\Services\Interfaces\ProfilDesaServiceInterface;
use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ProfilDesaService implements ProfilDesaServiceInterface
{
    protected ProfilDesaRepositoryInterface $profilDesaRepository;
    protected PerangkatDesaRepositoryInterface $perangkatDesaRepository;

    public function __construct(
        ProfilDesaRepositoryInterface $profilDesaRepository,
        PerangkatDesaRepositoryInterface $perangkatDesaRepository
    ) {
        $this->profilDesaRepository = $profilDesaRepository;
        $this->perangkatDesaRepository = $perangkatDesaRepository;
    }

    /**
     * Get profil desa for admin management
     */
    public function getProfilDesa(): ProfilDesa
    {
        $defaultData = [
            'nama_desa' => 'Nama Desa Anda',
            'alamat' => 'Alamat lengkap kantor desa.',
            'email' => 'email@desa.id',
            'telepon' => '081234567890',
            'nama_kepala_desa' => 'Nama Kepala Desa',
            'misi' => json_encode([]),
        ];

        return $this->profilDesaRepository->createOrGet($defaultData);
    }

    /**
     * Update profil desa
     */
    public function updateProfil(Request $request): ProfilDesa
    {
        $validated = $this->validateRequest($request);
        $profil = $this->profilDesaRepository->getFirst();

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->handleLogoUpload(
                $request->file('logo'),
                $profil->logo
            );
        } else {
            unset($validated['logo']);
        }

        // Pastikan misi disimpan sebagai JSON, minimal []
        $validated['misi'] = isset($validated['misi']) && is_array($validated['misi'])
            ? json_encode(array_values(array_filter($validated['misi'])))
            : json_encode([]);

        return $this->profilDesaRepository->update($profil, $validated);
    }

    /**
     * Get profil desa data as array for public display
     */
    public function getProfilDesaArray(): array
    {
        $profil = $this->profilDesaRepository->getFirst();
        return $profil->toArray();
    }

    /**
     * Get sejarah content for public display
     */
    public function getSejarahContent(): array
    {
        $profil = $this->profilDesaRepository->getFirst();

        return [
            'pageTitle' => 'Sejarah Desa',
            'content' => $profil->sejarah ?? 'Sejarah desa belum diisi.',
            'contentType' => 'html',
            'profilDesa' => $profil->toArray(),
        ];
    }

    /**
     * Get visi misi content for public display
     */
    public function getVisiMisiContent(): array
    {
        $profil = $this->profilDesaRepository->getFirst();

        return [
            'pageTitle' => 'Visi & Misi Desa',
            'content' => null,
            'contentType' => 'visi-misi',
            'profilDesa' => $profil->toArray(),
        ];
    }

    /**
     * Get struktur organisasi content for public display
     */
    public function getStrukturOrganisasiContent(): array
    {
        $profil = $this->profilDesaRepository->getFirst();
        
        // Get active perangkat desa sorted by jabatan urutan
        $perangkatAktif = $this->perangkatDesaRepository->getActiveSortedByJabatan();

        $strukturData = $perangkatAktif->map(function ($perangkat) {
            return [
                'jabatan' => $perangkat->jabatanDesa->nama_jabatan ?? 'Belum diset',
                'nama' => $perangkat->nama ?? 'Belum diisi',
                'foto' => $perangkat->foto,
                'foto_url' => $perangkat->foto_url,
            ];
        });

        return [
            'pageTitle' => 'Struktur Organisasi',
            'content' => $strukturData,
            'contentType' => 'struktur',
            'profilDesa' => $profil->toArray(),
        ];
    }

    /**
     * Get content by page type for public display
     */
    public function getContentByPage(string $page): array
    {
        return match ($page) {
            'sejarah' => $this->getSejarahContent(),
            'visi-misi' => $this->getVisiMisiContent(),
            'struktur-organisasi' => $this->getStrukturOrganisasiContent(),
            default => $this->handleInvalidPage($page),
        };
    }

    /**
     * Handle logo upload
     */
    public function handleLogoUpload(?object $file, ?string $currentLogo): ?string
    {
        if (!$file) {
            return null;
        }

        if ($currentLogo && Storage::disk('public')->exists($currentLogo)) {
            Storage::disk('public')->delete($currentLogo);
        }

        return $file->store('logo-desa', 'public');
    }

    /**
     * Handle invalid page requests
     */
    private function handleInvalidPage(string $page): never
    {
        throw new HttpResponseException(
            response()->json([
                'message' => "Halaman '{$page}' tidak ditemukan.",
                'available_pages' => ['sejarah', 'visi-misi', 'struktur-organisasi']
            ], Response::HTTP_NOT_FOUND)
        );
    }

    /**
     * Validate request for update
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'nama_desa' => 'required|string|max:255',
            'nama_kecamatan' => 'required|string|max:255',
            'nama_kabupaten' => 'required|string|max:255',
            'nama_provinsi' => 'required|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'alamat' => 'required|string|max:1000',
            'email' => 'required|email|max:255',
            'telepon' => 'required|string|max:20',
            'nama_kepala_desa' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'sejarah' => 'nullable|string',
            'visi' => 'nullable|string',
            'misi' => 'nullable|array',
            'misi.*' => 'nullable|string|max:1000',
        ]);
    }
}