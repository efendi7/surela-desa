<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PerangkatDesa;
use App\Models\JabatanDesa;
use App\Models\ProfilDesa;
use Carbon\Carbon;

class PerangkatDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan ada minimal satu profil desa. Jika tidak, buat satu.
        $profil = ProfilDesa::firstOrCreate(['nama_desa' => 'Desa Surela']);

        $kades = JabatanDesa::where('nama_jabatan', 'Kepala Desa')->first();
        $sekdes = JabatanDesa::where('nama_jabatan', 'Sekretaris Desa')->first();
        $kaurKeuangan = JabatanDesa::where('nama_jabatan', 'Kaur Keuangan')->first();

        $perangkats = [
            [
                'profil_desa_id' => $profil->id,
                'jabatan_desa_id' => $kades->id,
                'nama' => 'Budi Santoso', // * Required
                'nik' => '3374010101800001', // Optional
                'nip' => null, // Optional
                'jenis_kelamin' => 'L', // Optional
                'tempat_lahir' => 'Semarang', // Optional
                'tanggal_lahir' => '1980-01-01', // Optional
                'alamat' => 'Jl. Pahlawan No. 1, Desa Surela', // Optional
                'telepon' => '081234567890', // Optional
                'email' => 'budi.kades@example.com', // Optional
                'status_kepegawaian' => 'Honorer', // * Required
                'pangkat_golongan' => null, // Optional - for Honorer usually null
                'pendidikan_terakhir' => 'S1', // Optional
                'tanggal_mulai' => Carbon::now()->subYears(2), // * Required
                'tanggal_selesai' => null, // Optional - null means still active
                'status_jabatan' => 'aktif', // * Required
                'foto' => null, // Optional
                'dokumen' => null, // Optional
                'catatan' => 'Kepala Desa terpilih periode 2022-2028', // Optional
            ],
            [
                'profil_desa_id' => $profil->id,
                'jabatan_desa_id' => $sekdes->id,
                'nama' => 'Siti Aminah', // * Required
                'nik' => '3374010202850002', // Optional
                'nip' => '197502022008012001', // Optional
                'jenis_kelamin' => 'P', // Optional
                'tempat_lahir' => 'Demak', // Optional
                'tanggal_lahir' => '1985-02-02', // Optional
                'alamat' => 'Jl. Merdeka No. 10, Desa Surela', // Optional
                'telepon' => '081234567891', // Optional
                'email' => 'siti.sekdes@example.com', // Optional
                'status_kepegawaian' => 'PNS', // * Required
                'pangkat_golongan' => 'III/a', // Optional - for PNS usually filled
                'pendidikan_terakhir' => 'S1', // Optional
                'tanggal_mulai' => Carbon::now()->subYears(5), // * Required
                'tanggal_selesai' => null, // Optional
                'status_jabatan' => 'aktif', // * Required
                'foto' => null, // Optional
                'dokumen' => null, // Optional
                'catatan' => 'Sekretaris Desa dengan pengalaman 5 tahun', // Optional
            ],
            [
                'profil_desa_id' => $profil->id,
                'jabatan_desa_id' => $kaurKeuangan->id,
                'nama' => 'Joko Susilo', // * Required
                'nik' => '3374010303900003', // Optional
                'nip' => null, // Optional
                'jenis_kelamin' => 'L', // Optional
                'tempat_lahir' => 'Kendal', // Optional
                'tanggal_lahir' => '1990-03-03', // Optional
                'alamat' => 'Jl. Kartini No. 5, Desa Surela', // Optional
                'telepon' => '081234567892', // Optional
                'email' => 'joko.kaur@example.com', // Optional
                'status_kepegawaian' => 'Honorer', // * Required
                'pangkat_golongan' => null, // Optional - for Honorer usually null
                'pendidikan_terakhir' => 'D3', // Optional
                'tanggal_mulai' => Carbon::now()->subYear(), // * Required
                'tanggal_selesai' => null, // Optional
                'status_jabatan' => 'aktif', // * Required
                'foto' => null, // Optional
                'dokumen' => null, // Optional
                'catatan' => 'Lulusan D3 Akuntansi dengan sertifikat komputer', // Optional
            ],
        ];

        foreach ($perangkats as $perangkat) {
            PerangkatDesa::create($perangkat);
        }
    }
}