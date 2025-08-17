<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa yang mengajukan
            $table->foreignId('jenis_surat_id')->constrained()->onDelete('cascade'); // Surat apa yang diajukan
            $table->json('data_pemohon'); // Snapshot data pemohon (nama, NIK, dll) saat pengajuan
            $table->json('lampiran')->nullable(); // Path ke file lampiran yang di-upload
            $table->enum('status', ['pending', 'diproses', 'selesai', 'ditolak'])->default('pending');
            $table->text('keterangan_admin')->nullable(); // Catatan dari admin (misal: alasan ditolak)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surats');
    }
};
