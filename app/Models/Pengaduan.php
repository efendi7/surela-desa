<?php
// app/Models/Pengaduan.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'timeline', // Kolom JSON untuk menyimpan timeline pengaduan
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
        'timeline' => 'array', // Cast kolom timeline sebagai arrayw
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

      public function getFotoBuktiUrlAttribute()
    {
        if ($this->foto_bukti) {
            // Storage::url() akan otomatis membuat URL yang benar
            return Storage::url($this->foto_bukti);
        }
        // Berikan gambar placeholder jika tidak ada foto
        return 'https://placehold.co/600x400?text=No+Image';
    }
    
    /**
     * [TAMBAHKAN METHOD INI JUGA]
     * Lakukan hal yang sama untuk foto_proses agar konsisten.
     */
    public function getFotoProsesUrlAttribute()
    {
        if ($this->foto_proses) {
            return Storage::url($this->foto_proses);
        }
        return null; // Atau return placeholder jika perlu
    }
    
    /**
     * [TAMBAHKAN INI]
     * Beritahu Laravel untuk selalu menyertakan atribut custom ini saat model diubah ke array/JSON.
     */
    protected $appends = ['foto_bukti_url', 'foto_proses_url'];

}