<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. User dengan role 'admin'
        User::create([
            'name' => 'Efendi',
            'email' => 'efendi.admin@surela.desa.id',
            'role' => 'admin',
            'password' => Hash::make('admin123'), // Ganti 'password' dengan password yang aman
            'email_verified_at' => now(),
            'nik' => '1234567890123456',
            'phone' => '081234567890',
            'address' => 'Jl. Pahlawan No. 1, Desa Surela',
            'tempat_lahir' => 'Demak',
            'tanggal_lahir' => '2003-24-07',
            'jenis_kelamin' => 'Laki-Laki',
            'pekerjaan' => 'Perangkat Desa',
            'agama' => 'Islam',
            'status_perkawinan' => 'Belum Menikah',
            'kewarganegaraan' => 'Indonesia',
        ]);

        // 2. Membuat 5 user dengan role 'warga' secara spesifik
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => 'Warga ' . $i,
                'email' => 'warga' . $i . '@surela.desa.id',
                'role' => 'warga',
                'password' => Hash::make('warga123'),
                'email_verified_at' => now(),
                'nik' => '111111111111111' . $i, // NIK unik untuk setiap warga
                'phone' => '08987654321' . $i, // Nomor telepon unik
                'address' => 'Dusun Harapan Jaya No. ' . (10 + $i),
                'tempat_lahir' => 'Kabupaten Semarang',
                'tanggal_lahir' => (1995 + $i) . '-0' . $i . '-20',
                'jenis_kelamin' => ($i % 2 == 0) ? 'Perempuan' : 'Laki-Laki',
                'pekerjaan' => 'Wiraswasta',
                'agama' => 'Islam',
                'status_perkawinan' => ($i % 2 == 0) ? 'Belum Menikah' : 'Menikah',
                'kewarganegaraan' => 'Indonesia',
            ]);
        }
    }
}

