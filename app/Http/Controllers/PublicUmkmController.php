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
        // Base Query - hanya UMKM yang disetujui dengan relasi user
        $query = Umkm::where('status', 'disetujui')
            ->with('user:id,name,nik')
            ->latest();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_usaha', 'like', "%{$search}%")
                    ->orWhere('nama_pemilik', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('kategori_usaha', 'like', "%{$search}%");
            });
        }

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_usaha', $request->kategori);
        }

        // Filter by pemilik
        if ($request->filled('pemilik')) {
            $query->where('nama_pemilik', 'like', "%{$request->pemilik}%");
        }

        // Pagination - lebih banyak item untuk public view
        $umkms = $query->paginate(12)->withQueryString();

        // Get available categories for filter dropdown
        $availableCategories = Umkm::where('status', 'disetujui')
            ->distinct()
            ->pluck('kategori_usaha')
            ->filter()
            ->sort()
            ->values();

        // Format foto_pendukung untuk semua UMKM di list
        foreach ($umkms->items() as $umkm) {
            if ($umkm->foto_pendukung && is_string($umkm->foto_pendukung) && $umkm->foto_pendukung !== '') {
                try {
                    $umkm->foto_pendukung = json_decode($umkm->foto_pendukung, true) ?: [];
                } catch (\Exception $e) {
                    $umkm->foto_pendukung = [];
                }
            } else {
                $umkm->foto_pendukung = [];
            }
        }

        return Inertia::render('Public/UMKM/Index', [
            'umkms' => [
                'data' => $umkms->items(),
                'links' => $umkms->toArray()['links'],
                'current_page' => $umkms->currentPage(),
                'last_page' => $umkms->lastPage(),
                'per_page' => $umkms->perPage(),
                'total' => $umkms->total(),
                'from' => $umkms->firstItem(),
                'to' => $umkms->lastItem(),
            ],
            'filters' => [
                'search' => $request->search,
                'kategori' => $request->kategori,
                'pemilik' => $request->pemilik,
            ],
            'availableCategories' => $availableCategories,
        ]);
    }

    /**
     * API endpoint untuk mendapatkan detail UMKM (digunakan oleh modal)
     */
    public function getDetail(Umkm $umkm)
    {
        // Validasi status
        if ($umkm->status !== 'disetujui') {
            return response()->json([
                'error' => 'UMKM tidak ditemukan atau belum disetujui.'
            ], 404);
        }

        // Load relasi user
        $umkm->load('user:id,name,nik,email');

        // Format foto_pendukung untuk UMKM utama
        if ($umkm->foto_pendukung && is_string($umkm->foto_pendukung) && $umkm->foto_pendukung !== '') {
            try {
                $umkm->foto_pendukung = json_decode($umkm->foto_pendukung, true) ?: [];
            } catch (\Exception $e) {
                $umkm->foto_pendukung = [];
            }
        } else {
            $umkm->foto_pendukung = [];
        }

        // Get related UMKMs (same category, different owner)
        $relatedUmkms = Umkm::where('status', 'disetujui')
            ->where('kategori_usaha', $umkm->kategori_usaha)
            ->where('id', '!=', $umkm->id)
            ->with('user:id,name')
            ->limit(4)
            ->get();

        // Format foto_pendukung untuk related UMKMs
        foreach ($relatedUmkms as $related) {
            if ($related->foto_pendukung && is_string($related->foto_pendukung) && $related->foto_pendukung !== '') {
                try {
                    $related->foto_pendukung = json_decode($related->foto_pendukung, true) ?: [];
                } catch (\Exception $e) {
                    $related->foto_pendukung = [];
                }
            } else {
                $related->foto_pendukung = [];
            }
        }

        return response()->json([
            'umkm' => $umkm,
            'relatedUmkms' => $relatedUmkms,
        ]);
    }

    /**
     * Get UMKM statistics for public display
     */
    public function statistics(): Response
    {
        $totalUmkm = Umkm::where('status', 'disetujui')->count();
        
        $stats = [
            'total_umkm' => $totalUmkm,
            'categories_count' => Umkm::where('status', 'disetujui')
                ->distinct('kategori_usaha')
                ->count(),
            'active_today' => (int) floor($totalUmkm * 0.7), // Mock data untuk "aktif hari ini"
            'by_category' => Umkm::where('status', 'disetujui')
                ->selectRaw('kategori_usaha, COUNT(*) as count')
                ->groupBy('kategori_usaha')
                ->get()
                ->mapWithKeys(fn($item) => [$item->kategori_usaha => $item->count])
                ->toArray(),
        ];

        return Inertia::render('Public/UMKM/Statistics', [
            'statistics' => $stats,
        ]);
    }
}