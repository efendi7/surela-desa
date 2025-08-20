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
        Schema::table('jenis_surats', function (Blueprint $table) {
            // 1. Hapus kolom slug dan kode_surat
            $table->dropColumn(['slug', 'kode_surat']);

            // 2. Tambahkan kolom template_surat setelah kolom deskripsi
            $table->text('template_surat')->nullable()->after('deskripsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            // 1. Tambahkan kembali kolom slug dan kode_surat
            $table->string('slug');
            $table->string('kode_surat');

            // 2. Hapus kolom template_surat
            $table->dropColumn('template_surat');
        });
    }
};