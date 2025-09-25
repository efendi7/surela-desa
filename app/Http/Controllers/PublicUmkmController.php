<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicUmkmController extends Controller
{
    /**
     * Tampilkan daftar UMKM yang disetujui untuk publik.
     */
    public function index(Request $request): Response
    {
        // Base Query
        $query = Umkm::where('status', 'disetujui')
            ->with('user:id,name,nik')
            ->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_usaha', 'like', "%{$search}%")
                    ->orWhere('nama_pemilik', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_usaha', $request->kategori);
        }

        // Pagination (12 per page)
        $umkms = $query->paginate(12)->withQueryString();

        // Kirim data sesuai struktur di Vue
        return Inertia::render('Public/UMKM/Index', [
            'umkms' => [
                // data
                'data'  => $umkms->items(),
                // links untuk pagination inertia
                'links' => $umkms->toArray()['links'],
                // info meta
                'current_page' => $umkms->currentPage(),
                'last_page'    => $umkms->lastPage(),
                'per_page'     => $umkms->perPage(),
                'total'        => $umkms->total(),
                'from'         => $umkms->firstItem(),
                'to'           => $umkms->lastItem(),
            ],
            'search'   => $request->search,
            'kategori' => $request->kategori,
        ]);
    }

    /**
     * Detail UMKM publik.
     */
    public function show(Umkm $umkm): Response
    {
        // Hanya tampilkan jika disetujui
        if ($umkm->status !== 'disetujui') {
            abort(404);
        }

        $umkm->load('user:id,name,nik');

        return Inertia::render('Public/UMKM/Show', [
            'umkm' => $umkm,
        ]);
    }
}
