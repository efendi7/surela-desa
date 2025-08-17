<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\ProfilDesa;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            // Add flash messages here
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'profilDesa' => function () {
                // Mengambil data profil, atau membuat data default jika belum ada.
                // Ini memastikan data selalu tersedia.
                 return ProfilDesa::firstOrCreate(['id' => 1], [
                    'nama_desa' => 'SURELA Desa',
                    'nama_kecamatan' => 'Nama Kecamatan', // Diubah
                    'nama_kabupaten' => 'Nama Kabupaten', // Diubah
                    'nama_provinsi' => 'Nama Provinsi',   // Ditambahkan
                    'alamat' => 'Alamat lengkap kantor desa.',
                    'email' => 'email@desa.id',
                    'telepon' => '081234567890',
                    'nama_kepala_desa' => 'Nama Kepala Desa',
                ]);
            },
        ];
    }
}