<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProfilDesa; // Pastikan model ProfilDesa sudah dibuat

class ProfilDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProfilDesa::create([
            'nama_desa' => 'Desa Surela',
            'nama_kecamatan' => 'Kecamatan Mijen',
            'nama_kabupaten' => 'Kota Semarang',
            'nama_provinsi' => 'Jawa Tengah',
            'alamat' => 'Jl. Raya Desa Surela No. 123, Mijen, Kota Semarang, Jawa Tengah',
            'kode_pos' => '50219',
            'email' => 'info@surela.desa.id',
            'telepon' => '024-1234567',
            'nama_kepala_desa' => 'Budi Santoso', // Bisa diisi nanti dari PerangkatDesaSeeder
            'visi' => 'Mewujudkan Desa Surela yang Maju, Mandiri, dan Sejahtera Berlandaskan Gotong Royong.',
            'misi' => "1. Meningkatkan kualitas sumber daya manusia.\n2. Mengembangkan potensi ekonomi desa.\n3. Meningkatkan infrastruktur desa.",
            'sejarah' => 'Desa Surela didirikan pada tahun 1920 oleh para pendahulu yang memiliki semangat juang tinggi...',
        ]);
    }
}
