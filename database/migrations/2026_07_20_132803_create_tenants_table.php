<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama HIMA / Organisasi (misal: HMIF AMIKOM)
            $table->string('slug')->unique(); // URL unik (misal: hmif-amikom)
            $table->string('email')->unique();
            $table->string('password');
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};