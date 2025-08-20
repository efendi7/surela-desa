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
        Schema::table('profil_desas', function (Blueprint $table) {
            $table->text('sejarah')->nullable()->after('logo');
            $table->text('visi')->nullable()->after('sejarah');
            $table->text('misi')->nullable()->after('visi');
            
            // PERUBAHAN DI SINI: Mengubah tipe kolom menjadi JSON
            // Ini memungkinkan kita menyimpan data terstruktur (jabatan dan nama).
            $table->json('struktur_organisasi')->nullable()->after('misi'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_desas', function (Blueprint $table) {
            // Menambahkan logika untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn(['sejarah', 'visi', 'misi', 'struktur_organisasi']);
        });
    }
};