<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_xx_xx_xxxxxx_add_timeline_to_pengajuan_surats_table.php
public function up(): void
{
    Schema::table('pengajuan_surats', function (Blueprint $table) {
        $table->json('timeline')->nullable()->after('lampiran'); // Menambahkan kolom timeline
    });
}

public function down(): void
{
    Schema::table('pengajuan_surats', function (Blueprint $table) {
        $table->dropColumn('timeline');
    });
}
};
