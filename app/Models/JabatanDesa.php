<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JabatanDesa extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jabatan_desas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_jabatan',
        'deskripsi',
        'level',
        'urutan',
        'is_active',
    ];

    /**
     * Get the perangkat desa for the jabatan.
     */
    public function perangkatDesa(): HasMany
    {
        return $this->hasMany(PerangkatDesa::class);
    }
}
