<?php

namespace App\Http\Controllers;

use App\Models\ProfilDesa;
use App\Models\PengajuanSurat;
use App\Models\User;
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
                'nama_desa' => 'Desa Kebumen',
                'nama_kecamatan' => 'Pringsurat',
                'nama_kabupaten' => 'Temanggung',
                'nama_provinsi' => 'Jawa Tengah',
                'alamat' => 'Kecamatan Pringsurat, Kabupaten Temanggung, Jawa Tengah',
                'email' => 'desakebumen@temanggungkab.go.id',
                'telepon' => '(0293) 123456',
                'nama_kepala_desa' => 'Bapak Slamet Riyadi',
                'logo' => null,
                'kode_pos' => '56264',
                'visi' => 'Mewujudkan Desa Kebumen yang maju, mandiri, dan sejahtera',
                'misi' => [
                    'Meningkatkan kualitas pelayanan publik',
                    'Mengembangkan potensi ekonomi desa',
                    'Melestarikan budaya dan lingkungan hidup'
                ],
                'sejarah' => 'Desa Kebumen berdiri sejak tahun 1950...',
                'struktur_organisasi' => [
                    'kepala_desa' => 'Bapak Slamet Riyadi',
                    'sekretaris_desa' => 'Ibu Sri Wahyuni',
                    'bendahara' => 'Bapak Ahmad Sudrajat'
                ],
            ]
        );

        // Build alamat lengkap dari komponen yang terpisah
        $alamatLengkap = $profilDesa->alamat;
        if (empty($alamatLengkap) && ($profilDesa->nama_kecamatan || $profilDesa->nama_kabupaten || $profilDesa->nama_provinsi)) {
            $alamatLengkap = trim(
                ($profilDesa->nama_kecamatan ? 'Kecamatan ' . $profilDesa->nama_kecamatan . ', ' : '') .
                ($profilDesa->nama_kabupaten ? 'Kabupaten ' . $profilDesa->nama_kabupaten . ', ' : '') .
                ($profilDesa->nama_provinsi ? $profilDesa->nama_provinsi : ''), 
                ', '
            );
        }

        // Generate website URL berdasarkan nama desa
        $websiteUrl = $this->generateWebsiteUrl($profilDesa->nama_desa, $profilDesa->nama_kecamatan);

        return Inertia::render('LandingPage', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'profilDesa' => [
                'nama_desa' => $profilDesa->nama_desa,
                'nama_kecamatan' => $profilDesa->nama_kecamatan,
                'nama_kabupaten' => $profilDesa->nama_kabupaten,
                'nama_provinsi' => $profilDesa->nama_provinsi,
                'alamat' => $alamatLengkap,
                'email' => $profilDesa->email,
                'telepon' => $profilDesa->telepon,
                'nama_kepala_desa' => $profilDesa->nama_kepala_desa,
                'logo' => $profilDesa->logo,
                'kode_pos' => $profilDesa->kode_pos,
                'website' => $websiteUrl,
                'visi' => $profilDesa->visi,
                'misi' => $profilDesa->misi,
                'sejarah' => $profilDesa->sejarah,
                'struktur_organisasi' => $profilDesa->struktur_organisasi,
            ],
            'features' => $this->getFeatures(),
            'statistics' => $this->getStatistics(),
        ]);
    }

    /**
     * Generate website URL berdasarkan nama desa dan kecamatan
     */
    private function generateWebsiteUrl($namaDesa, $namaKecamatan)
    {
        if (empty($namaDesa)) {
            return 'www.desa.go.id';
        }

        $desaSlug = strtolower(str_replace(' ', '', $namaDesa));
        $kecamatanSlug = $namaKecamatan ? strtolower(str_replace(' ', '', $namaKecamatan)) : 'kec';
        
        return "www.{$desaSlug}-{$kecamatanSlug}.desa.id";
    }

    /**
     * Mendapatkan daftar fitur yang tersedia
     */
    private function getFeatures()
    {
        return [
            [
                'title' => 'Surat Keterangan Online',
                'description' => 'Ajukan surat keterangan domisili, SKCK, surat kematian, dan surat penting lainnya tanpa antre.',
                'icon' => 'document-text',
                'bgColor' => 'bg-blue-50',
                'iconColor' => 'text-blue-600',
                'borderColor' => 'border-blue-200',
                'route' => 'surat.index',
            ],
            [
                'title' => 'Pengaduan Masyarakat',
                'description' => 'Sampaikan keluhan infrastruktur, kebersihan, dan masalah desa lainnya dengan mudah.',
                'icon' => 'chat-bubble-left-right',
                'bgColor' => 'bg-green-50',
                'iconColor' => 'text-green-600',
                'borderColor' => 'border-green-200',
                'route' => 'laporan.index',
            ],
            [
                'title' => 'Pendaftaran UMKM',
                'description' => 'Daftarkan usaha pertanian, kerajinan, dan warung untuk bantuan modal usaha dari pemerintah.',
                'icon' => 'building-storefront',
                'bgColor' => 'bg-orange-50',
                'iconColor' => 'text-orange-600',
                'borderColor' => 'border-orange-200',
                'route' => 'umkm.index',
            ],
            [
                'title' => 'Berita & Pengumuman',
                'description' => 'Info terbaru tentang program desa, kegiatan gotong royong, dan pengumuman penting.',
                'icon' => 'megaphone',
                'bgColor' => 'bg-purple-50',
                'iconColor' => 'text-purple-600',
                'borderColor' => 'border-purple-200',
                'route' => 'informasi.index',
            ],
            [
                'title' => 'Lacak Pengajuan',
                'description' => 'Pantau status pengajuan surat dan bantuan sosial secara real-time melalui smartphone.',
                'icon' => 'chart-pie',
                'bgColor' => 'bg-indigo-50',
                'iconColor' => 'text-indigo-600',
                'borderColor' => 'border-indigo-200',
                'route' => 'tracking.index',
            ],
            [
                'title' => 'Profil Desa Kebumen',
                'description' => 'Pelajari potensi wisata, produk unggulan, sejarah, dan data demografis Desa Kebumen.',
                'icon' => 'building-library',
                'bgColor' => 'bg-teal-50',
                'iconColor' => 'text-teal-600',
                'borderColor' => 'border-teal-200',
                'route' => 'profil-desa.index',
            ],
            [
                'title' => 'Program KKN',
                'description' => 'Informasi kegiatan mahasiswa KKN, program pemberdayaan masyarakat, dan kolaborasi pendidikan.',
                'icon' => 'academic-cap',
                'bgColor' => 'bg-yellow-50',
                'iconColor' => 'text-yellow-600',
                'borderColor' => 'border-yellow-200',
                'route' => 'kkn.index',
            ],
            [
                'title' => 'Potensi Desa',
                'description' => 'Eksplorasi potensi pertanian, pariwisata, dan ekonomi kreatif untuk kemajuan bersama.',
                'icon' => 'chart-bar',
                'bgColor' => 'bg-red-50',
                'iconColor' => 'text-red-600',
                'borderColor' => 'border-red-200',
                'route' => 'potensi.index',
            ],
        ];
    }

    /**
     * Mendapatkan statistik layanan
     */
    private function getStatistics()
    {
        // Mengambil jumlah warga terdaftar dari database
        $totalWarga = User::where('role', 'warga')->count();
        
        // Mengambil jumlah surat terbit (status 'selesai') dari database
        $suratTerbit = PengajuanSurat::where('status', 'selesai')->count();
        
        return [
            [
                'value' => number_format($totalWarga, 0, ',', '.') . '+',
                'label' => 'Warga Terdaftar',
                'color' => 'text-purple-600',
                'icon' => 'users',
            ],
            [
                'value' => number_format($suratTerbit, 0, ',', '.') . '+',
                'label' => 'Surat Terbit',
                'color' => 'text-blue-600',
                'icon' => 'document-text',
            ],
            [
                'value' => '45',
                'label' => 'UMKM Terdaftar',
                'color' => 'text-orange-600',
                'icon' => 'building-storefront',
            ],
            [
                'value' => '98%',
                'label' => 'Kepuasan Warga',
                'color' => 'text-green-600',
                'icon' => 'heart',
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
                'nama_desa' => $profilDesa->nama_desa ?? 'Desa Kebumen',
                'alamat' => $profilDesa->alamat ?? 'Kecamatan Pringsurat, Kabupaten Temanggung, Jawa Tengah',
                'email' => $profilDesa->email ?? 'desakebumen@temanggungkab.go.id',
                'telepon' => $profilDesa->telepon ?? '(0293) 123456',
                'nama_kepala_desa' => $profilDesa->nama_kepala_desa ?? 'Kepala Desa',
                'kode_pos' => $profilDesa->kode_pos ?? '56264',
                'jam_pelayanan' => 'Senin - Jumat: 08:00 - 15:00 WIB, Sabtu: 08:00 - 12:00 WIB',
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
                'pengaduan_masyarakat' => true,
                'umkm' => true,
                'berita_pengumuman' => true,
                'tracking' => true,
                'profil_desa' => true,
                'program_kkn' => true,
                'potensi_desa' => true,
            ]
        ]);
    }
}