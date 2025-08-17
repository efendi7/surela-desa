<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanSurat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'jenis_surat_id',
        'data_pemohon',
        'lampiran',
        'status',
        'keterangan_admin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_pemohon' => 'array',
        'lampiran' => 'array',
    ];

    /**
     * Mendapatkan data user yang membuat pengajuan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan data jenis surat yang diajukan.
     */
    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }
}
