<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_details_to_pengaduan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            // Informasi lokasi
            $table->string('alamat')->nullable()->after('foto_proses');
            $table->decimal('latitude', 10, 8)->nullable()->after('alamat');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');

            // Kategori pengaduan
            $table->string('kategori')->nullable()->after('longitude');
            $table->enum('prioritas', ['Rendah', 'Sedang', 'Tinggi', 'Darurat'])->default('Sedang')->after('kategori');

            // Tracking admin
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null')->after('prioritas');
            $table->text('keterangan_admin')->nullable()->after('admin_id');

            // Estimasi penyelesaian
            $table->date('estimasi_selesai')->nullable()->after('keterangan_admin');
            $table->date('tanggal_selesai')->nullable()->after('estimasi_selesai');

            // Rating dan feedback
            $table->integer('rating')->nullable()->after('tanggal_selesai');
            $table->text('feedback')->nullable()->after('rating');

            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index(['kategori', 'prioritas']);
        });
    }

    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            // Hapus foreign key dan index terlebih dahulu
            $table->dropForeign(['admin_id']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['kategori', 'prioritas']);

            // Hapus kolom-kolomnya
            $table->dropColumn([
                'alamat', 'latitude', 'longitude', 'kategori', 'prioritas',
                'admin_id', 'keterangan_admin', 'estimasi_selesai',
                'tanggal_selesai', 'rating', 'feedback'
            ]);
        });
    }
};