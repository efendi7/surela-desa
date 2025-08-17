<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 16)->unique()->nullable()->after('email');
            $table->string('phone', 15)->nullable()->after('nik');
            $table->enum('role', ['admin', 'warga'])->default('warga')->after('phone');
            $table->text('address')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik', 'phone', 'role', 'address']);
        });
    }
};