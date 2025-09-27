<?php

namespace App\Http\Controllers;

use App\Models\Berita as Post; // Menggunakan alias 'Post' agar lebih generik
use Inertia\Inertia;

class PublicPengumumanController extends Controller
{
    /**
     * Menampilkan daftar semua pengumuman yang sudah dipublikasikan.
     */
    public function index()
    {
        return Inertia::render('Public/Pengumuman/Index', [
            // Menggunakan scope Pengumuman() dan Published() yang sudah dibuat di model
            'pengumumans' => Post::pengumuman()
                ->published()
                ->latest('published_at')
                ->paginate(9),
        ]);
    }

    /**
     * Menampilkan detail satu pengumuman berdasarkan slug.
     */
    public function show($slug)
    {
        $pengumuman = Post::pengumuman()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();
            
        return Inertia::render('Public/Pengumuman/Show', [
            // Eager load relasi user untuk menampilkan nama penulis
            'pengumuman' => $pengumuman->load('user:id,name'),
        ]);
    }
}
