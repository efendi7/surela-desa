<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_surats', function (Blueprint $table) {
            // untuk simpan nomor urut (per bulan/jenis surat kalau perlu)
            $table->unsignedInteger('increment_nomor')->nullable()->after('status');
            // untuk simpan nomor surat lengkap (contoh: 001/SKD/08/2025)
            $table->string('nomor_surat')->nullable()->after('increment_nomor');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_surats', function (Blueprint $table) {
            $table->dropColumn(['increment_nomor', 'nomor_surat']);
        });
    }
};
