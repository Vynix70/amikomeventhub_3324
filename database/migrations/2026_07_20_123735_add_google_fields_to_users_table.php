<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            // Menambahkan kolom google_id dan avatar setelah kolom password
            $blueprint->string('google_id')->nullable()->after('password');
            $blueprint->string('avatar')->nullable()->after('google_id');
            
            // Karena login via Google tidak wajib mengisi password di awal, kita buat password jadi nullable
            $blueprint->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['google_id', 'avatar']);
        });
    }
};