<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ticket_tiers', function (Blueprint $table) {
            // Hanya tambah start_date jika belum ada
            if (!Schema::hasColumn('ticket_tiers', 'start_date')) {
                $table->dateTime('start_date')->nullable()->after('quota');
            }
            // Hanya tambah end_date jika belum ada
            if (!Schema::hasColumn('ticket_tiers', 'end_date')) {
                $table->dateTime('end_date')->nullable()->after('start_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ticket_tiers', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_tiers', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('ticket_tiers', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }
};