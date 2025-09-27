<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Berita extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
         'type',
        'slug',
        'content',
        'image',
        'file_path',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Mendefinisikan bahwa slug akan digunakan sebagai kunci rute.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Relasi ke model User (pembuat berita).
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope untuk hanya mengambil berita yang sudah dipublikasikan.
     */
    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now());
    }
     public function scopeBerita($query)
    {
        return $query->where('type', 'berita');
    }

    public function scopePengumuman($query)
    {
        return $query->where('type', 'pengumuman');
    }
     public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}