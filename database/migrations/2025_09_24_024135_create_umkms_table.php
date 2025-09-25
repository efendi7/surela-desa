<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_usaha');
            $table->string('nama_pemilik');
            $table->string('nik_pemilik', 16);
            $table->text('alamat_usaha');
            $table->text('deskripsi');
            $table->string('kategori_usaha'); // Contoh: Kuliner, Kerajinan, Jasa, dll.
            $table->string('nomor_telepon');
            $table->string('foto_produk')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable(); // Catatan dari admin jika ditolak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};
