<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // 1. Import class Str untuk membuat slug

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
        'kode_surat',
        'slug', // 2. Tambahkan 'slug' ke fillable
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
     * 3. Tambahkan method booted() ini.
     * Method ini akan dieksekusi secara otomatis oleh Laravel.
     */
    protected static function booted(): void
    {
        // Event 'creating' berjalan SEBELUM data baru disimpan
        static::creating(function (JenisSurat $jenisSurat) {
            // Membuat slug dari nama_surat
            $jenisSurat->slug = Str::slug($jenisSurat->nama_surat);

            // Membuat kode_surat dari singkatan nama
            $words = explode(' ', $jenisSurat->nama_surat);
            $kode = '';
            foreach ($words as $word) {
                $kode .= strtoupper(substr($word, 0, 1));
            }
            $jenisSurat->kode_surat = $kode;
        });

        // Event 'updating' berjalan SEBELUM data di-update
        static::updating(function (JenisSurat $jenisSurat) {
            // Cek apakah nama_surat diubah
            if ($jenisSurat->isDirty('nama_surat')) {
                // Jika ya, buat ulang slug dan kode_surat
                $jenisSurat->slug = Str::slug($jenisSurat->nama_surat);

                $words = explode(' ', $jenisSurat->nama_surat);
                $kode = '';
                foreach ($words as $word) {
                    $kode .= strtoupper(substr($word, 0, 1));
                }
                $jenisSurat->kode_surat = $kode;
            }
        });
    }
    
}