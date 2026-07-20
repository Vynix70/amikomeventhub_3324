<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Menambahkan status: pending (baru daftar), verified (layak), rejected (ditolak)
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};