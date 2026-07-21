<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'ticket_tier_id')) {
                $table->foreignId('ticket_tier_id')
                      ->nullable()
                      ->after('event_id')
                      ->constrained('ticket_tiers')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'ticket_tier_id')) {
                $table->dropForeign(['ticket_tier_id']);
                $table->dropColumn('ticket_tier_id');
            }
        });
    }
};