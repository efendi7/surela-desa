<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanSurat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_surat_id',
        'data_pemohon',
        'lampiran',
        'status',
        'keterangan_admin',
        'increment_nomor',   // tambahkan ini
        'nomor_surat',       // tambahkan ini
    ];

    protected $casts = [
        'data_pemohon' => 'array',
        'lampiran' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }
}
