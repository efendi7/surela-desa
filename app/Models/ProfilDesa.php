<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilDesa extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
    ];
}
