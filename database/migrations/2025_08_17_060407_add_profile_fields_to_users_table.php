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
        Schema::table('users', function (Blueprint $table) {
            $table->string('pekerjaan', 100)->nullable()->after('jenis_kelamin');
            $table->string('agama', 50)->nullable()->after('pekerjaan');
            $table->enum('status_perkawinan', ['Belum Menikah', 'Menikah', 'Cerai'])->nullable()->after('agama');
            $table->string('kewarganegaraan', 50)->default('Indonesia')->after('status_perkawinan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pekerjaan', 'agama', 'status_perkawinan', 'kewarganegaraan']);
        });
    }
};
