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
        Schema::create('ticket_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Contoh: 'Early Bird', 'Presale 1', 'Regular'
            $table->decimal('price', 12, 2); // Harga tiket untuk tier ini
            $table->integer('quota'); // Jumlah kuota total awal untuk tier ini
            $table->integer('remaining_quota')->nullable(); // Kuota tersisa (otomatis terkurangi saat checkout)
            $table->dateTime('start_date'); // Tanggal & Jam mulai tier ini aktif dijual
            $table->dateTime('end_date'); // Tanggal & Jam terakhir tier ini berlaku
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_tiers');
    }
};