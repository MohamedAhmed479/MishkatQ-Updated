<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds FSRS (Free Spaced Repetition Scheduler) fields:
     * - stability: Days until retrievability drops to 90% (memory strength)
     * - difficulty: How hard this item is for the user (1-10 scale)
     */
    public function up(): void
    {
        Schema::table('spaced_repetitions', function (Blueprint $table) {
            // Stability: Number of days for retrievability to drop to 90%
            // Higher stability = stronger memory
            $table->float('stability')->default(1.0)->after('ease_factor')
                ->comment('FSRS: Days until retrievability drops to 90%');

            // Difficulty: How hard this item is for the user (1-10 scale)
            // Lower = easier, Higher = harder
            $table->float('difficulty')->default(5.0)->after('stability')
                ->comment('FSRS: Item difficulty on 1-10 scale');

            // Add index for efficient querying by stability (for finding weak items)
            $table->index('stability');
            $table->index('difficulty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spaced_repetitions', function (Blueprint $table) {
            $table->dropIndex(['stability']);
            $table->dropIndex(['difficulty']);
            $table->dropColumn(['stability', 'difficulty']);
        });
    }
};
