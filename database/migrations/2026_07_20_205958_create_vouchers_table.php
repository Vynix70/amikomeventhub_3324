<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Contoh: MAHASISWA50
            $table->enum('type', ['fixed', 'percentage']); // Potongan harga Rupiah tetap atau Persentase %
            $table->decimal('discount_value', 10, 2); // Nilai potongannya (misal: 50000 untuk rupiah atau 10 untuk 10%)
            $table->integer('quota')->default(10); // Batas kuota pemakaian voucher
            $table->date('expires_at'); // Tanggal kadaluarsa kupon
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};