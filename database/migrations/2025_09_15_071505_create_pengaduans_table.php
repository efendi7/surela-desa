<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_pengaduan_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('foto_bukti'); // Path ke file foto bukti dari warga
            $table->enum('status', ['Dikirim', 'Diterima', 'Diproses', 'Selesai'])->default('Dikirim');
            $table->string('foto_proses')->nullable(); // Path ke file foto bukti proses dari admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};