<?php
// app/Models/Pengaduan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak mengikuti konvensi Laravel (opsional)
    protected $table = 'pengaduan';

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'foto_bukti',
        'status',
        'foto_proses',
    ];

    /**
     * Relasi ke model User (pembuat pengaduan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}