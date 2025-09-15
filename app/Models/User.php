<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute; // [PERUBAHAN] Pastikan ini ada
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'nik',
        'phone', // Nama kolom yang benar adalah 'phone'
        'role',
        'address', // Nama kolom yang benar adalah 'address'
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'pekerjaan',
        'agama',
        'status_perkawinan',
        'kewarganegaraan',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'profile_photo_url',
        'is_profile_complete', // [PERUBAHAN] Tambahkan accessor ini ke appends
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
        ];
    }

    /**
     * Get the URL to the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return 'https://api.dicebear.com/8.x/initials/svg?seed=' . urlencode($this->name);
    }

    /**
     * [PERUBAHAN] Accessor untuk mengecek apakah profil pengguna sudah lengkap.
     * Logika terpusat di sini.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function isProfileComplete(): Attribute
    {
        return Attribute::make(
            get: fn () => !empty($this->nik) &&
                         !empty($this->address) && // Menggunakan nama kolom yang benar
                         !empty($this->phone) // Menambahkan pengecekan nomor telepon
        );
    }
}