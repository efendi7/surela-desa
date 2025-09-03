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
        'increment_nomor',
        'nomor_surat',
        'file_word', 
        'file_final',
        'file_hasil',
        'timeline'
    ];

    protected $casts = [
        'data_pemohon' => 'array',
        'lampiran' => 'array',
        'timeline' => 'array',
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
