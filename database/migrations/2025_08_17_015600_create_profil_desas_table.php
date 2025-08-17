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
        Schema::create('profil_desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa');
            $table->string('nama_kecamatan');
            $table->string('nama_kabupaten');
            $table->string('nama_provinsi');
            $table->text('alamat');
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->string('nama_kepala_desa')->nullable();
            $table->string('logo')->nullable(); // Path ke file logo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_desas');
    }
};
