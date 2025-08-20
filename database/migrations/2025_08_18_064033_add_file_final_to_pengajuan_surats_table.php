<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // ...
public function up(): void
{
    Schema::table('pengajuan_surats', function (Blueprint $table) {
        // Menyimpan path file surat yang sudah direvisi dan diupload admin
        $table->string('file_final')->nullable()->after('keterangan_admin');
    });
}

public function down(): void
{
    Schema::table('pengajuan_surats', function (Blueprint $table) {
        $table->dropColumn('file_final');
    });
}
// ...
};
