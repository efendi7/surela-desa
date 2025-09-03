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
    Schema::table('pengajuan_surats', function (Blueprint $table) {
        // Baris ini akan mengubah kolom 'data_pemohon' agar boleh kosong (nullable)
        $table->text('data_pemohon')->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_surats', function (Blueprint $table) {
            //
        });
    }
};
