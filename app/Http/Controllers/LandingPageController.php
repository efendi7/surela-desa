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
            // Ambil data profil desa, jika tidak ada, gunakan nilai default sementara
            $profilDesa = ProfilDesa::firstOrNew(
                ['id' => 1],
                [
                    'nama_desa' => 'Nama Desa',
                    'alamat' => 'Alamat Kantor Desa',
                    'logo' => null,
                ]
            );

            return Inertia::render('LandingPage', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'profilDesa' => $profilDesa,
            ]);
        }
    }
    