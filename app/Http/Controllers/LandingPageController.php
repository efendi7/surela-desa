<?php

namespace App\Http\Controllers;

use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

class LandingPageController extends Controller
{
    /**
     * Menampilkan halaman landing page.
     */
    public function index()
    {
        // Ambil data profil desa lengkap, jika tidak ada, gunakan nilai default
        $profilDesa = ProfilDesa::firstOrNew(
            ['id' => 1],
            [
                'nama_desa' => 'SURELA Desa',
                'alamat' => 'Alamat lengkap desa Anda.',
                'email' => 'email@desa.id',
                'telepon' => '081234567890',
                'logo' => null,
                'visi' => null,
                'misi' => null,
                'sejarah' => null,
            ]
        );

        // Pastikan data email dan telepon tersedia
        if (!$profilDesa->email) {
            $profilDesa->email = 'email@desa.id';
        }
        
        if (!$profilDesa->telepon) {
            $profilDesa->telepon = '081234567890';
        }

        if (!$profilDesa->nama_desa) {
            $profilDesa->nama_desa = 'SURELA Desa';
        }

        if (!$profilDesa->alamat) {
            $profilDesa->alamat = 'Alamat lengkap desa Anda.';
        }

        return Inertia::render('LandingPage', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'profilDesa' => [
                'nama_desa' => $profilDesa->nama_desa,
                'alamat' => $profilDesa->alamat,
                'email' => $profilDesa->email,
                'telepon' => $profilDesa->telepon,
                'logo' => $profilDesa->logo,
                'visi' => $profilDesa->visi,
                'misi' => $profilDesa->misi,
                'sejarah' => $profilDesa->sejarah,
            ],
            'features' => $this->getFeatures(),
            'statistics' => $this->getStatistics(),
        ]);
    }

    /**
     * Mendapatkan daftar fitur yang tersedia
     */
    private function getFeatures()
    {
        return [
            [
                'title' => 'Surat Online',
                'description' => 'Buat surat keterangan domisili, usaha, dan surat penting lainnya dari rumah.',
                'icon' => 'document-text',
                'bgColor' => 'bg-blue-50',
                'iconColor' => 'text-blue-600',
                'borderColor' => 'border-blue-200',
                'route' => 'surat.index',
            ],
            [
                'title' => 'Laporan Warga',
                'description' => 'Sampaikan keluhan atau saran untuk kemajuan desa dengan mudah.',
                'icon' => 'chat-bubble-left-right',
                'bgColor' => 'bg-green-50',
                'iconColor' => 'text-green-600',
                'borderColor' => 'border-green-200',
                'route' => 'laporan.index',
            ],
            [
                'title' => 'Daftar UMKM',
                'description' => 'Daftarkan usaha kecil Anda untuk mendapat bantuan pemerintah.',
                'icon' => 'building-storefront',
                'bgColor' => 'bg-orange-50',
                'iconColor' => 'text-orange-600',
                'borderColor' => 'border-orange-200',
                'route' => 'umkm.index',
            ],
            [
                'title' => 'Informasi Desa',
                'description' => 'Dapatkan berita terbaru dan pengumuman penting dari desa.',
                'icon' => 'megaphone',
                'bgColor' => 'bg-purple-50',
                'iconColor' => 'text-purple-600',
                'borderColor' => 'border-purple-200',
                'route' => 'informasi.index',
            ],
            [
                'title' => 'Lacak Status',
                'description' => 'Pantau perkembangan pengajuan surat dan layanan Anda secara langsung.',
                'icon' => 'chart-pie',
                'bgColor' => 'bg-indigo-50',
                'iconColor' => 'text-indigo-600',
                'borderColor' => 'border-indigo-200',
                'route' => 'tracking.index',
            ],
            [
                'title' => 'Profil Desa',
                'description' => 'Pelajari sejarah, visi misi, dan struktur pemerintahan desa.',
                'icon' => 'building-library',
                'bgColor' => 'bg-teal-50',
                'iconColor' => 'text-teal-600',
                'borderColor' => 'border-teal-200',
                'route' => 'profil-desa.index',
            ],
        ];
    }

    /**
     * Mendapatkan statistik layanan
     */
    private function getStatistics()
    {
        return [
            [
                'value' => '24 Jam',
                'label' => 'Layanan Online',
                'color' => 'text-blue-600',
                'icon' => 'clock',
            ],
            [
                'value' => 'Gratis',
                'label' => 'Untuk Warga',
                'color' => 'text-green-600',
                'icon' => 'gift',
            ],
            [
                'value' => '5 Menit',
                'label' => 'Proses Cepat',
                'color' => 'text-orange-600',
                'icon' => 'lightning-bolt',
            ],
            [
                'value' => 'Aman',
                'label' => 'Data Terlindungi',
                'color' => 'text-purple-600',
                'icon' => 'shield-check',
            ],
        ];
    }

    /**
     * Menampilkan halaman demo atau tutorial
     */
    public function demo()
    {
        return Inertia::render('Demo', [
            'features' => $this->getFeatures(),
        ]);
    }

    /**
     * Mendapatkan informasi kontak desa
     */
    public function contact()
    {
        $profilDesa = ProfilDesa::first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'nama_desa' => $profilDesa->nama_desa ?? 'SURELA Desa',
                'alamat' => $profilDesa->alamat ?? 'Alamat lengkap desa Anda.',
                'email' => $profilDesa->email ?? 'email@desa.id',
                'telepon' => $profilDesa->telepon ?? '081234567890',
                'jam_pelayanan' => 'Senin - Jumat: 08:00 - 16:00 WIB',
            ]
        ]);
    }

    /**
     * Endpoint untuk mendapatkan status sistem
     */
    public function status()
    {
        return response()->json([
            'success' => true,
            'status' => 'active',
            'message' => 'Sistem berjalan normal',
            'timestamp' => now(),
            'services' => [
                'surat_online' => true,
                'laporan_warga' => true,
                'umkm' => true,
                'informasi' => true,
                'tracking' => true,
                'profil_desa' => true,
            ]
        ]);
    }
}