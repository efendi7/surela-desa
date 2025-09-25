<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class AdminUmkmController extends Controller
{
    /**
     * Display a listing of pending UMKM entries.
     */
    public function index(Request $request): Response
    {
        $query = Umkm::with('user:id,name')->where('status', 'pending')->latest();

        $umkms = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/UMKM/Index', [
            'umkms' => $umkms,
            'filters' => $request->only('status'),
        ]);
    }

    /**
     * Display a listing of approved and rejected UMKM entries (history).
     */
    public function history(Request $request): Response
    {
        $query = Umkm::with('user:id,name')
            ->whereIn('status', ['disetujui', 'ditolak'])
            ->latest();

        $filters = $request->only('status');
        if (isset($filters['status']) && in_array($filters['status'], ['disetujui', 'ditolak'])) {
            $query->where('status', $filters['status']);
        }

        $umkms = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/UMKM/History', [
            'umkms' => $umkms,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified UMKM resource.
     */
    public function show(Umkm $umkm): Response
    {
        $umkm->load('user:id,name,email');
        
        return Inertia::render('Admin/UMKM/Show', [
            'umkm' => $umkm
        ]);
    }

    /**
     * Update the status and admin notes of the specified UMKM.
     */
    public function updateStatus(Request $request, Umkm $umkm): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan_admin' => [
                'nullable',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('status') === 'ditolak' && empty($value)) {
                        $fail('Catatan admin wajib diisi ketika status ditolak.');
                    }
                },
            ],
        ]);

        $umkm->update([
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'] ?? null,
        ]);

        return redirect()->route('admin.umkm.index')->with('success', 'Status UMKM berhasil diperbarui.');
    }

    /**
     * Remove the specified UMKM from storage.
     */
    public function destroy(Umkm $umkm): RedirectResponse
    {
        if ($umkm->foto_produk) {
            Storage::delete($umkm->foto_produk);
        }
        if ($umkm->foto_pendukung) {
            foreach ($umkm->foto_pendukung as $foto) {
                Storage::delete($foto);
            }
        }
        $umkm->delete();

        return redirect()->route('admin.umkm.index')->with('success', 'Data UMKM telah dihapus.');
    }
}