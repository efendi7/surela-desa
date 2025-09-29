<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Ubah kolom status ke VARCHAR untuk memodifikasi ENUM.
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->string('status')->change();
        });
        
        // Step 2: Update data lama jika diperlukan (opsional, berdasarkan kode Anda).
        DB::statement("UPDATE pengaduan SET status = 'Pending' WHERE status IN ('Dikirim', 'Diterima')");
        
        // Step 3: Ubah kembali ke ENUM dengan daftar nilai yang baru, termasuk 'ditolak'.
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Diproses', 'Selesai', 'ditolak']) // 'ditolak' ditambahkan di sini
                  ->default('Pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Ubah kolom ke VARCHAR untuk memodifikasi ENUM.
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->string('status')->change();
        });
        
        // Step 2: Sebelum menghapus 'ditolak' dari ENUM, ubah data yang ada ke status lain yang valid.
        // Ini untuk mencegah error data integrity. Di sini kita ubah ke 'Pending'.
        DB::statement("UPDATE pengaduan SET status = 'Pending' WHERE status = 'ditolak'");
        
        // Step 3: Kembalikan data 'Pending' ke nilai lama (sesuai logika rollback Anda).
        DB::statement("UPDATE pengaduan SET status = 'Dikirim' WHERE status = 'Pending'");
        
        // Step 4: Kembalikan skema ENUM ke kondisi sebelum migrasi ini dijalankan.
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->enum('status', ['Dikirim', 'Diterima', 'Diproses', 'Selesai'])
                  ->default('Dikirim')
                  ->change();
        });
    }
};