<?php
namespace App\Services;

use App\Repositories\Interfaces\ProfilDesaRepositoryInterface;
use App\Services\Interfaces\ProfilDesaServiceInterface;
use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProfilDesaService implements ProfilDesaServiceInterface
{
    protected ProfilDesaRepositoryInterface $profilDesaRepository;

    public function __construct(ProfilDesaRepositoryInterface $profilDesaRepository)
    {
        $this->profilDesaRepository = $profilDesaRepository;
    }

    public function getProfilDesa(): ProfilDesa
    {
        $defaultStruktur = $this->profilDesaRepository->getDefaultStrukturOrganisasi();
        
        $defaultData = [
            'nama_desa' => 'Nama Desa Anda',
            'alamat' => 'Alamat lengkap kantor desa.',
            'email' => 'email@desa.id',
            'telepon' => '081234567890',
            'nama_kepala_desa' => 'Nama Kepala Desa',
            'struktur_organisasi' => $defaultStruktur,
        ];

        $profil = $this->profilDesaRepository->createOrGet($defaultData);

        // Jika profil sudah ada tapi struktur_organisasi kosong/null, isi dengan default
        if (empty($profil->struktur_organisasi)) {
            $profil = $this->profilDesaRepository->update($profil, [
                'struktur_organisasi' => $defaultStruktur
            ]);
        }

        return $profil;
    }

    public function updateProfil(Request $request): ProfilDesa
    {
        $validated = $this->validateRequest($request);
        $profil = $this->profilDesaRepository->getFirst();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->handleLogoUpload(
                $request->file('logo'), 
                $profil->logo
            );
        }

        return $this->profilDesaRepository->update($profil, $validated);
    }

    public function handleLogoUpload(?object $file, ?string $currentLogo): ?string
    {
        if (!$file) {
            return null;
        }

        // Delete old logo if exists
        if ($currentLogo && Storage::disk('public')->exists($currentLogo)) {
            Storage::disk('public')->delete($currentLogo);
        }

        return $file->store('logo-desa', 'public');
    }

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
            'misi' => 'nullable|string',
            'struktur_organisasi' => 'present|array',
            'struktur_organisasi.*.jabatan' => 'required|string',
            'struktur_organisasi.*.nama' => 'nullable|string|max:255',
        ]);
    }
}