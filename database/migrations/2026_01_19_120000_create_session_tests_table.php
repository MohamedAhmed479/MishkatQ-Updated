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
        // Create session_tests table to store test results
        Schema::create('session_tests', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('plan_item_id');
            $table->foreign('plan_item_id')->references('id')->on('plan_items')->onDelete('cascade');

            // Test type: 'recitation', 'gap_filling', 'verse_ordering', 'verse_beginning'
            $table->enum('test_type', ['recitation', 'gap_filling', 'verse_ordering', 'verse_beginning']);

            // Score as percentage (0-100)
            $table->decimal('score', 5, 2)->default(0);

            // Pass/Fail status (threshold is configurable, default 70%)
            $table->boolean('passed')->default(false);

            // Test attempt number (to track retries)
            $table->integer('attempt_number')->default(1);

            // Duration in seconds
            $table->integer('duration_seconds')->nullable();

            // Detailed results stored as JSON
            // For recitation: { transcript: string, errors: [], accuracy: number }
            // For gap_filling: { questions: [], answers: [], correct_count: number }
            // For verse_ordering: { original_order: [], user_order: [], correct: boolean }
            $table->json('details')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'plan_item_id']);
            $table->index(['plan_item_id', 'test_type']);
        });

        // Add test requirement preference to user_preferences
        Schema::table('user_preferences', function (Blueprint $table) {
            // Whether to require passing tests before marking as completed
            $table->boolean('require_test_before_completion')->default(true)->after('current_level');
            
            // Minimum score required to pass (percentage)
            $table->integer('minimum_test_score')->default(70)->after('require_test_before_completion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropColumn(['require_test_before_completion', 'minimum_test_score']);
        });

        Schema::dropIfExists('session_tests');
    }
};
