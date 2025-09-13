<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPerangkat extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'riwayat_perangkat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'perangkat_desa_id',
        'jabatan_desa_lama_id',
        'jabatan_desa_baru_id',
        'jenis_perubahan',
        'tanggal_efektif',
        'keterangan',
        'dokumen_pendukung',
        'data_lama',
        'data_baru',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_efektif' => 'date',
        'data_lama' => 'array',
        'data_baru' => 'array',
    ];

    /**
     * Get the perangkat desa that owns the riwayat.
     */
    public function perangkatDesa(): BelongsTo
    {
        return $this->belongsTo(PerangkatDesa::class);
    }

    /**
     * Get the old jabatan for the riwayat.
     */
    public function jabatanLama(): BelongsTo
    {
        return $this->belongsTo(JabatanDesa::class, 'jabatan_desa_lama_id');
    }

    /**
     * Get the new jabatan for the riwayat.
     */
    public function jabatanBaru(): BelongsTo
    {
        return $this->belongsTo(JabatanDesa::class, 'jabatan_desa_baru_id');
    }
}
