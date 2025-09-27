<?php

namespace App\Http\Controllers;

use App\Models\Berita as Post; // Menggunakan alias 'Post'
use Inertia\Inertia;

class PublicBeritaController extends Controller
{
    /**
     * Menampilkan daftar semua berita yang sudah dipublikasikan.
     */
    public function index()
    {
        return Inertia::render('Public/Berita/Index', [
            // ✨ PERUBAHAN: Menambahkan scope berita() untuk memfilter
            'beritas' => Post::berita() 
                ->published()
                ->latest('published_at')
                ->paginate(9),
        ]);
    }

    /**
     * Menampilkan detail satu berita berdasarkan slug.
     */
    public function show($slug)
    {
        // ✨ PERUBAHAN: Menambahkan scope berita() untuk memastikan hanya berita yang bisa diakses
        $berita = Post::berita()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();
            
        return Inertia::render('Public/Berita/Show', [
            'berita' => $berita->load('user:id,name'),
        ]);
    }
}
