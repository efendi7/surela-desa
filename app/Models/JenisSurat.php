<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSurat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_surat',
        'deskripsi',
        'template_surat', // Menggantikan slug dan kode_surat
        'syarat',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'syarat' => 'array',
    ];

    /**
     * Definisikan relasi one-to-many ke Pengajuan.
     * Sebuah JenisSurat dapat memiliki banyak Pengajuan.
     */
    public function pengajuan()
    {
        // Pastikan 'jenis_surat_id' adalah nama foreign key yang benar
        // di dalam tabel 'pengajuans' Anda.
        return $this->hasMany(PengajuanSurat::class, 'jenis_surat_id');
    }
}
