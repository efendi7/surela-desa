<?php

// app/Http/Controllers/PublicProfilController.php
namespace App\Http\Controllers;

use App\Services\Interfaces\ProfilDesaServiceInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicProfilController extends Controller
{
    public function __construct(
        private ProfilDesaServiceInterface $profilDesaService
    ) {}

    /**
     * Menampilkan halaman profil desa untuk publik.
     *
     * @param string $page
     * @return Response
     */
    public function show(string $page): Response
    {
        // Mendapatkan konten berdasarkan halaman dari service
        $data = $this->profilDesaService->getContentByPage($page);

        // Render komponen Vue dengan data yang diperlukan
        return Inertia::render('ProfilDesa/Index', $data);
    }
}