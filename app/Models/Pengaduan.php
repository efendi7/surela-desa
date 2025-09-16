<?php
// app/Models/Pengaduan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengaduan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'pengaduan';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'foto_bukti',
        'status',
        'foto_proses',
        // --- TAMBAHAN ---
        'alamat',
        'latitude',
        'longitude',
        'kategori',
        'prioritas',
        'admin_id',
        'keterangan_admin',
        'estimasi_selesai',
        'tanggal_selesai',
        'rating',
        'feedback',
    ];

    /**
     * Atribut yang harus di-casting ke tipe data tertentu.
     * Berguna agar kolom tanggal dikenali sebagai objek Carbon.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estimasi_selesai' => 'date',
        'tanggal_selesai' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Mendapatkan data user (warga) yang membuat pengaduan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * --- TAMBAHAN ---
     * Mendapatkan data user (admin) yang menangani pengaduan.
     */
    public function admin(): BelongsTo
    {
        // Relasi ini menunjuk ke model User, menggunakan foreign key 'admin_id'
        return $this->belongsTo(User::class, 'admin_id');
    }
}