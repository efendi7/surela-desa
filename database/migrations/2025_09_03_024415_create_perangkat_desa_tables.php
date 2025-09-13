<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel master jabatan
        Schema::create('jabatan_desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jabatan');
            $table->text('deskripsi')->nullable();
            $table->string('level')->default('staff'); // contoh: kepala, sekretaris, kaur, staff
            $table->integer('urutan')->default(0); // untuk sorting tampilan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique('nama_jabatan');
            $table->index(['is_active', 'urutan']);
        });

        // Tabel perangkat desa
        Schema::create('perangkat_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profil_desa_id')->constrained()->onDelete('cascade');
            // Menunjuk ke tabel 'jabatan_desas' yang sudah diperbaiki
            $table->foreignId('jabatan_desa_id')->constrained('jabatan_desas')->onDelete('restrict');
            
            // Data pribadi
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->string('nik', 16)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email')->nullable();
            
            // Data kepegawaian
            $table->enum('status_kepegawaian', ['PNS', 'PPPK', 'Honorer', 'Kontrak'])->default('Honorer');
            $table->string('pangkat_golongan')->nullable();
            $table->text('pendidikan_terakhir')->nullable();
            
            // Periode jabatan
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status_jabatan', ['aktif', 'non_aktif', 'cuti', 'mutasi'])->default('aktif');
            
            // Media
            $table->string('foto')->nullable();
            $table->json('dokumen')->nullable(); // SK, sertifikat, dll
            
            // Metadata
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['profil_desa_id', 'status_jabatan']);
            // Memberikan nama kustom yang lebih pendek untuk index
            $table->index(['jabatan_desa_id', 'tanggal_mulai', 'tanggal_selesai'], 'idx_perangkat_jabatan_periode');
            $table->index('nama');
            $table->unique(['nik'], 'unique_nik');
        });

        // Tabel riwayat jabatan (opsional, untuk audit trail)
        Schema::create('riwayat_perangkat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perangkat_desa_id')->constrained('perangkat_desa')->onDelete('cascade');
            // Menunjuk ke tabel 'jabatan_desas'
            $table->foreignId('jabatan_desa_lama_id')->nullable()->constrained('jabatan_desas');
            $table->foreignId('jabatan_desa_baru_id')->nullable()->constrained('jabatan_desas');
            
            $table->enum('jenis_perubahan', [
                'pengangkatan', 'mutasi', 'promosi', 'demosi', 
                'pemberhentian', 'pensiun', 'meninggal', 'update_data'
            ]);
            $table->date('tanggal_efektif');
            $table->text('keterangan')->nullable();
            $table->string('dokumen_pendukung')->nullable(); // file SK, dll
            
            $table->json('data_lama')->nullable(); // snapshot data sebelum perubahan
            $table->json('data_baru')->nullable(); // snapshot data setelah perubahan
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->index(['perangkat_desa_id', 'tanggal_efektif']);
            $table->index('jenis_perubahan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('riwayat_perangkat');
        Schema::dropIfExists('perangkat_desa');
        Schema::dropIfExists('jabatan_desas'); // Diperbarui menjadi 'jabatan_desas'
    }
};
