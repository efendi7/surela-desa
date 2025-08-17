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
    /**
     * Menampilkan halaman profil desa.
     */
    public function index()
    {
        // Mengambil data profil, atau membuat data default jika belum ada.
        // Nama kolom disesuaikan dengan migrasi dan model yang baru.
        $profil = ProfilDesa::firstOrCreate(
            ['id' => 1],
            [
                'nama_desa' => 'Nama Desa Anda',
                'nama_kecamatan' => 'Nama Kecamatan',
                'nama_kabupaten' => 'Nama Kabupaten',
                'nama_provinsi' => 'Nama Provinsi',
                 'kode_pos' => '12345',
                'alamat' => 'Alamat lengkap kantor desa.',
                'email' => 'email@desa.id',
                'telepon' => '081234567890',
                'nama_kepala_desa' => 'Nama Kepala Desa',
            ]
        );

        return Inertia::render('Admin/ProfilDesa/Index', [
            'profilDesa' => $profil,
        ]);
    }

    /**
     * Memperbarui data profil desa.
     */
    public function update(Request $request)
    {
        $profil = ProfilDesa::firstOrFail();

        // Aturan validasi disesuaikan dengan nama kolom yang baru.
        // Aturan untuk kolom yang tidak ada di model (seperti visi, misi) dihapus untuk menghindari error.
        $validated = $request->validate([
            'nama_desa' => 'required|string|max:255',
            'nama_kecamatan' => 'required|string|max:255',
            'nama_kabupaten' => 'required|string|max:255',
            'nama_provinsi' => 'required|string|max:255',
            'kode_pos' => 'nullable|string|max:10', // 👈 tambahkan ini
            'alamat' => 'required|string|max:1000',
            'email' => 'required|email|max:255',
            'telepon' => 'required|string|max:20',
            'nama_kepala_desa' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                // Delete old logo if exists
                if ($profil->logo && Storage::disk('public')->exists($profil->logo)) {
                    Storage::disk('public')->delete($profil->logo);
                }

                // Store new logo
                $logoPath = $request->file('logo')->store('logo-desa', 'public');
                $validated['logo'] = $logoPath;
            } catch (\Exception $e) {
                throw ValidationException::withMessages([
                    'logo' => ['Gagal mengupload logo. Silakan coba lagi.']
                ]);
            }
        }

        $profil->update($validated);

        return redirect()
            ->route('admin.profil-desa.index')
            ->with('success', 'Profil desa berhasil diperbarui.');
    }

    /**
     * Hapus logo desa.
     */
    public function deleteLogo()
    {
        $profil = ProfilDesa::firstOrFail();

        if ($profil->logo && Storage::disk('public')->exists($profil->logo)) {
            Storage::disk('public')->delete($profil->logo);
            $profil->update(['logo' => null]);

            return redirect()
                ->route('admin.profil-desa.index')
                ->with('success', 'Logo desa berhasil dihapus.');
        }

        return redirect()
            ->route('admin.profil-desa.index')
            ->with('error', 'Logo tidak ditemukan.');
    }
}
