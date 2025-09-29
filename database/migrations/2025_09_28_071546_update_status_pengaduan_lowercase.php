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
        // Step 1: ubah kolom ke string supaya fleksibel
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->string('status')->change();
        });

        // Step 2: ubah semua status menjadi lowercase
        DB::statement("UPDATE pengaduan SET status = LOWER(status)");

        // Opsional: mapping khusus, kalau dulu ada status 'Dikirim', 'Diterima' → jadi pending
        DB::statement("UPDATE pengaduan SET status = 'pending' WHERE status IN ('dikirim','diterima')");

        // Step 3: ubah kembali ke ENUM lowercase
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->enum('status', ['pending','diproses','selesai','ditolak'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Balik ke format lama (kapital)
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->string('status')->change();
        });

        DB::statement("UPDATE pengaduan SET status = 'Dikirim' WHERE status = 'pending'");
        DB::statement("UPDATE pengaduan SET status = 'Diproses' WHERE status = 'diproses'");
        DB::statement("UPDATE pengaduan SET status = 'Selesai' WHERE status = 'selesai'");
        DB::statement("UPDATE pengaduan SET status = 'Diterima' WHERE status = 'ditolak'");

        Schema::table('pengaduan', function (Blueprint $table) {
            $table->enum('status', ['Dikirim','Diproses','Selesai','Diterima'])
                  ->default('Dikirim')
                  ->change();
        });
    }
};
