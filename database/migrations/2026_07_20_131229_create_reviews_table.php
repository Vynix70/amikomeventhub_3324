<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ulasan ke user yang login
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Menghubungkan ulasan ke event terkait
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            // Rating angka 1 sampai 5
            $table->tinyInteger('rating');
            // Isi ulasan/testimoni dari user
            $table->text('comment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};