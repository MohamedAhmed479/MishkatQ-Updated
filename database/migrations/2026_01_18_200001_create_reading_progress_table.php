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
        Schema::create('reading_progress', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('reading_plan_id');
            $table->foreign('reading_plan_id')->references('id')->on('reading_plans')->onDelete('cascade');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Pages read in this session
            $table->integer('pages_read')->default(0);

            // Range of pages read
            $table->integer('start_page');
            $table->integer('end_page');

            // Reading mode used
            $table->enum('reading_mode', ['hadr', 'tadabbur'])->default('hadr');

            // Duration in minutes (optional tracking)
            $table->integer('duration_minutes')->nullable();

            // Date of reading
            $table->date('date');

            // Completion status for the daily wird
            $table->boolean('daily_target_met')->default(false);

            // Notes or reflections (optional)
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['reading_plan_id', 'date']);
            $table->index(['user_id', 'date']);
            
            // Ensure one entry per plan per day
            $table->unique(['reading_plan_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_progress');
    }
};
