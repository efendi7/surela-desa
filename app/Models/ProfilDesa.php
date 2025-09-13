<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilDesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_desa',
        'nama_kecamatan',
        'nama_kabupaten',
        'nama_provinsi',
        'alamat',
        'email',
        'telepon',
        'nama_kepala_desa',
        'logo',
        'kode_pos',
        'sejarah',
        'visi',
        'misi',
        'struktur_organisasi',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'struktur_organisasi' => 'array',
        // --- TAMBAHKAN BARIS INI ---
        'misi' => 'array', 
    ];
}
