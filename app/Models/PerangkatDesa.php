<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage; // 1. Impor kelas Storage

class PerangkatDesa extends Model
{
    use HasFactory;

    protected $table = 'perangkat_desa';

    protected $fillable = [
        'profil_desa_id',
        'jabatan_desa_id',
        'nama',
        'nip',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'telepon',
        'email',
        'status_kepegawaian',
        'pangkat_golongan',
        'pendidikan_terakhir',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_jabatan',
        'foto',
        'dokumen',
        'catatan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'dokumen' => 'array',
    ];

    // 2. Tambahkan properti $appends untuk menyertakan accessor secara default
    protected $appends = ['foto_url'];

    // --- ACCESSORS & MUTATORS ---

    /**
     * 3. Accessor untuk mendapatkan URL lengkap dari foto.
     * Ini akan membuat atribut 'foto_url' tersedia di frontend.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return Storage::url($this->foto);
        }
        return null;
    }

    // --- RELATIONSHIPS ---

    /**
     * Relasi ke model ProfilDesa.
     */
    public function profilDesa(): BelongsTo
    {
        return $this->belongsTo(ProfilDesa::class);
    }

    /**
     * Relasi ke model JabatanDesa.
     */
    public function jabatanDesa(): BelongsTo
    {
        return $this->belongsTo(JabatanDesa::class);
    }

    /**
     * Relasi ke model RiwayatPerangkat.
     */
    public function riwayat(): HasMany
    {
        return $this->hasMany(RiwayatPerangkat::class);
    }
}