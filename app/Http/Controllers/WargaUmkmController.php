<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;

class WargaUmkmController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $umkms = Auth::user()->umkms()->latest()->paginate(10);
        return Inertia::render('Warga/UMKM/Index', [
            'umkms' => $umkms,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Warga/UMKM/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'alamat_usaha' => 'required|string',
            'deskripsi' => 'required|string',
            'kategori_usaha' => 'required|string|max:100',
            'nomor_telepon' => 'required|string|max:15',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_pendukung.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validasi untuk array foto pendukung
            'foto_pendukung' => 'array|max:3', // Maksimal 3 foto pendukung
        ]);

        $user = Auth::user();
        $umkmData = array_merge($validated, [
            'nama_pemilik' => $user->name,
            'nik_pemilik' => $user->nik,
        ]);

        // Simpan foto sampul
        if ($request->hasFile('foto_produk')) {
            $path = $request->file('foto_produk')->store('public/umkm_photos');
            $umkmData['foto_produk'] = $path;
        }

        // Simpan foto pendukung
        if ($request->hasFile('foto_pendukung')) {
            $fotoPendukung = [];
            foreach ($request->file('foto_pendukung') as $file) {
                $path = $file->store('public/umkm_photos');
                $fotoPendukung[] = $path;
            }
            $umkmData['foto_pendukung'] = json_encode($fotoPendukung);
        }

        $user->umkms()->create($umkmData);

        return to_route('warga.umkm.index')->with('success', 'Pendaftaran UMKM berhasil dikirim...');
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit(Umkm $umkm): Response
{
    $this->authorize('update', $umkm);

    // Ensure foto_pendukung is a valid JSON array
    $umkm->foto_pendukung = $umkm->foto_pendukung && is_string($umkm->foto_pendukung) && $umkm->foto_pendukung !== '' ? $umkm->foto_pendukung : json_encode([]);

    return Inertia::render('Warga/UMKM/Edit', [
        'umkm' => $umkm,
    ]);
}
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Umkm $umkm): RedirectResponse
    {
        $this->authorize('update', $umkm);

        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'nik_pemilik' => 'required|string|digits:16',
            'alamat_usaha' => 'required|string',
            'deskripsi' => 'required|string',
            'kategori_usaha' => 'required|string|max:100',
            'nomor_telepon' => 'required|string|max:15',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_pendukung.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_pendukung' => 'array|max:3',
        ]);

        // Simpan foto sampul baru
        if ($request->hasFile('foto_produk')) {
            if ($umkm->foto_produk) {
                Storage::delete($umkm->foto_produk);
            }
            $path = $request->file('foto_produk')->store('public/umkm_photos');
            $validated['foto_produk'] = $path;
        }

        // Simpan foto pendukung baru
        if ($request->hasFile('foto_pendukung')) {
            // Hapus foto pendukung lama jika ada
            if ($umkm->foto_pendukung) {
                foreach (json_decode($umkm->foto_pendukung, true) as $oldFoto) {
                    Storage::delete($oldFoto);
                }
            }
            $fotoPendukung = [];
            foreach ($request->file('foto_pendukung') as $file) {
                $path = $file->store('public/umkm_photos');
                $fotoPendukung[] = $path;
            }
            $validated['foto_pendukung'] = json_encode($fotoPendukung);
        }

        $validated['status'] = 'pending';
        $validated['catatan_admin'] = null;

        $umkm->update($validated);

        return to_route('warga.umkm.index')->with('success', 'Data UMKM berhasil diperbarui dan dikirim ulang untuk verifikasi.');
    }

    /**
     * Remove the specified resource from storage.
     */
   /**
 * Remove the specified resource from storage.
 */
public function destroy(Umkm $umkm): RedirectResponse
{
    $this->authorize('delete', $umkm);

    // Hapus foto sampul
    if ($umkm->foto_produk) {
        Storage::delete($umkm->foto_produk);
    }

    // Hapus foto pendukung
    if ($umkm->foto_pendukung && is_string($umkm->foto_pendukung) && $umkm->foto_pendukung !== '') {
        try {
            $fotoPendukung = json_decode($umkm->foto_pendukung, true);
            if (is_array($fotoPendukung)) {
                foreach ($fotoPendukung as $foto) {
                    Storage::delete($foto);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to parse foto_pendukung for UMKM ID ' . $umkm->id . ': ' . $e->getMessage());
        }
    }

    // Ganti ini: Hapus permanen dari database
    $umkm->forceDelete();  // <-- Ubah dari $umkm->delete() ke ini

    return to_route('warga.umkm.index')->with('success', 'Data UMKM berhasil dihapus secara permanen.');
}
}