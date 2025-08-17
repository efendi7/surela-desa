<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\JenisSurat;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            // Menambahkan kolom slug setelah kolom nama_surat
            $table->string('slug')->after('nama_surat')->nullable()->unique();
        });

        // Mengisi nilai slug untuk data yang sudah ada
        // Pastikan model JenisSurat sudah ada
        if (class_exists(JenisSurat::class)) {
            $jenisSurats = JenisSurat::all();
            foreach ($jenisSurats as $jenis) {
                $jenis->slug = Str::slug($jenis->nama_surat);
                $jenis->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
