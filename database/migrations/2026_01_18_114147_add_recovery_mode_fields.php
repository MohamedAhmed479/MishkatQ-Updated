<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds recovery mode support for smart plan adjustment
     */
    public function up(): void
    {
        // Add recovery mode tracking to memorization plans
        Schema::table('memorization_plans', function (Blueprint $table) {
            // When recovery mode was activated
            $table->timestamp('recovery_started_at')->nullable()->after('status')
                ->comment('Timestamp when recovery mode was activated');
        });

        // Add recovery item flag to spaced repetitions
        Schema::table('spaced_repetitions', function (Blueprint $table) {
            // Flag to mark items that are part of recovery mode
            $table->boolean('is_recovery_item')->default(false)->after('difficulty')
                ->comment('Whether this item is prioritized for recovery mode');
            
            $table->index('is_recovery_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memorization_plans', function (Blueprint $table) {
            $table->dropColumn('recovery_started_at');
        });

        Schema::table('spaced_repetitions', function (Blueprint $table) {
            $table->dropIndex(['is_recovery_item']);
            $table->dropColumn('is_recovery_item');
        });
    }
};
