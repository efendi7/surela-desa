<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Umkm extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nama_usaha',
        'nama_pemilik',
        'nik_pemilik',
        'alamat_usaha',
        'deskripsi',
        'kategori_usaha',
        'nomor_telepon',
        'foto_produk', // Foto sampul
        'foto_pendukung', // Foto pendukung (JSON)
        'status',
        'catatan_admin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'foto_pendukung' => 'array', // Cast kolom foto_pendukung sebagai array
    ];

    /**
     * Get the user that owns the UMKM.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}