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
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'nik' => $request->user()->nik,
                    'phone' => $request->user()->phone,
                    'address' => $request->user()->address,
                    'tempat_lahir' => $request->user()->tempat_lahir,
                    // PERBAIKAN: Format tanggal lahir ke Y-m-d agar sesuai dengan input HTML
                    'tanggal_lahir' => $request->user()->tanggal_lahir ? $request->user()->tanggal_lahir->format('Y-m-d') : null,
                    'jenis_kelamin' => $request->user()->jenis_kelamin,
                    'role' => $request->user()->role,
                ] : null,
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'profilDesa' => function () {
                return ProfilDesa::firstOrCreate(['id' => 1], [
                    'nama_desa' => 'SURELA Desa',
                    'nama_kecamatan' => 'Nama Kecamatan',
                    'nama_kabupaten' => 'Nama Kabupaten',
                    'nama_provinsi' => 'Nama Provinsi',
                    'alamat' => 'Alamat lengkap kantor desa.',
                    'email' => 'email@desa.id',
                    'telepon' => '081234567890',
                    'nama_kepala_desa' => 'Nama Kepala Desa',
                ]);
            },
        ]);
    }
}
