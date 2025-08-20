<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfilDesaController extends Controller
{
    public function index()
    {
        // Inisialisasi struktur organisasi sebagai array of objects
        $defaultStruktur = [
            ['jabatan' => 'Kepala Desa', 'nama' => ''],
            ['jabatan' => 'Badan Permusyawaratan Desa', 'nama' => ''],
            ['jabatan' => 'Sekretaris Desa', 'nama' => ''],
            ['jabatan' => 'Kaur Pemerintahan', 'nama' => ''],
            ['jabatan' => 'Kaur Pembangunan', 'nama' => ''],
            ['jabatan' => 'Kaur Pemberdayaan Masyarakat', 'nama' => ''],
            ['jabatan' => 'Kaur Kesejahteraan Rakyat', 'nama' => ''],
            ['jabatan' => 'Kaur Umum', 'nama' => ''],
            ['jabatan' => 'Kaur Keuangan', 'nama' => ''],
        ];

        $profil = ProfilDesa::firstOrCreate(
            ['id' => 1],
            [
                'nama_desa' => 'Nama Desa Anda',
                'alamat' => 'Alamat lengkap kantor desa.',
                'email' => 'email@desa.id',
                'telepon' => '081234567890',
                'nama_kepala_desa' => 'Nama Kepala Desa',
                // Simpan sebagai JSON
                'struktur_organisasi' => $defaultStruktur,
            ]
        );

        // Jika profil sudah ada tapi struktur_organisasi kosong/null, isi dengan default
        if (empty($profil->struktur_organisasi)) {
            $profil->struktur_organisasi = $defaultStruktur;
            $profil->save();
        }


        return Inertia::render('Admin/ProfilDesa/Index', [
            'profilDesa' => $profil,
        ]);
    }

    public function update(Request $request)
    {
        $profil = ProfilDesa::firstOrFail();

        // Validasi diubah untuk menerima array
        $validated = $request->validate([
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
            // Validasi untuk struktur organisasi
            'struktur_organisasi' => 'present|array',
            'struktur_organisasi.*.jabatan' => 'required|string',
            'struktur_organisasi.*.nama' => 'nullable|string|max:255',
        ]);
        
        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($profil->logo && Storage::disk('public')->exists($profil->logo)) {
                Storage::disk('public')->delete($profil->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logo-desa', 'public');
        }

        $profil->update($validated);

        return redirect()
            ->route('admin.profil-desa.index')
            ->with('success', 'Profil desa berhasil diperbarui.');
    }
}
