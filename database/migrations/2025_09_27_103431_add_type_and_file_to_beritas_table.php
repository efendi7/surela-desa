<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            // Kolom untuk membedakan 'berita' atau 'pengumuman'
            $table->enum('type', ['berita', 'pengumuman'])->default('berita')->after('id');
            
            // Kolom untuk menyimpan path file PDF, hanya untuk pengumuman
            $table->string('file_path')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            $table->dropColumn(['type', 'file_path']);
        });
    }
};