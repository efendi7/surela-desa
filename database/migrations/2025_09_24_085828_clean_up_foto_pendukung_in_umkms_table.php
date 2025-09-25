<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Set empty or invalid foto_pendukung to empty JSON array
        DB::table('umkms')
            ->whereNull('foto_pendukung')
            ->orWhere('foto_pendukung', '')
            ->orWhere('foto_pendukung', 'null')
            ->update(['foto_pendukung' => json_encode([])]);
    }

    public function down(): void
    {
        // Optional: Revert changes if needed
        DB::table('umkms')
            ->where('foto_pendukung', json_encode([]))
            ->update(['foto_pendukung' => null]);
    }
};