<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JabatanDesa;

class JabatanDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jabatans = [
            ['nama_jabatan' => 'Kepala Desa', 'level' => 'kepala', 'urutan' => 1],
            ['nama_jabatan' => 'Sekretaris Desa', 'level' => 'sekretaris', 'urutan' => 2],
            ['nama_jabatan' => 'Kaur Keuangan', 'level' => 'kaur', 'urutan' => 3],
            ['nama_jabatan' => 'Kaur Perencanaan', 'level' => 'kaur', 'urutan' => 4],
            ['nama_jabatan' => 'Kaur Umum dan Tata Usaha', 'level' => 'kaur', 'urutan' => 5],
            ['nama_jabatan' => 'Kasi Pemerintahan', 'level' => 'kasi', 'urutan' => 6],
            ['nama_jabatan' => 'Kasi Kesejahteraan', 'level' => 'kasi', 'urutan' => 7],
            ['nama_jabatan' => 'Kasi Pelayanan', 'level' => 'kasi', 'urutan' => 8],
            ['nama_jabatan' => 'Kepala Dusun I', 'level' => 'kadus', 'urutan' => 9],
            ['nama_jabatan' => 'Kepala Dusun II', 'level' => 'kadus', 'urutan' => 10],
            ['nama_jabatan' => 'Staf', 'level' => 'staf', 'urutan' => 11],
        ];

        foreach ($jabatans as $jabatan) {
            JabatanDesa::create($jabatan);
        }
    }
}
