<?php

namespace App\Http\Controllers;

use App\Models\ProfilDesa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicProfilController extends Controller
{
    /**
     * Menampilkan halaman profil desa untuk publik.
     */
    public function show($page)
    {
        $profil = ProfilDesa::firstOrFail(); // Gunakan firstOrFail untuk menangani jika profil tidak ada

        $view = 'Public/Profil/Index';
        $pageTitle = '';
        $content = null; // Diubah agar bisa menampung tipe data berbeda (string/array)
        $contentType = 'html'; // Tipe konten default

        switch ($page) {
            case 'sejarah':
                $pageTitle = 'Sejarah Desa';
                $content = $profil->sejarah ?? 'Sejarah desa belum diisi.';
                break;
            case 'visi-misi':
                $pageTitle = 'Visi & Misi Desa';
                $content = "<h2>Visi</h2>" . ($profil->visi ?? '<p>Visi belum diisi.</p>') . "<h2>Misi</h2>" . ($profil->misi ?? '<p>Misi belum diisi.</p>');
                break;
            case 'struktur-organisasi':
                $pageTitle = 'Struktur Organisasi';
                // PERUBAHAN: Mengirim data array langsung dari model
                $content = $profil->struktur_organisasi ?? [];
                // PERUBAHAN: Memberi penanda bahwa tipe kontennya adalah 'struktur'
                $contentType = 'struktur';
                break;
            default:
                abort(404);
        }

        return Inertia::render($view, [
            'pageTitle' => $pageTitle,
            'content' => $content,
            // PERUBAHAN: Mengirim contentType ke frontend
            'contentType' => $contentType,
        ]);
    }
}
