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
        Schema::create('reading_plans', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('name');
            $table->text('description')->nullable();

            // Plan type: sequential (full Quran from start) or custom (specific range)
            $table->enum('type', ['sequential', 'custom'])->default('sequential');

            // Target type: pages, juzs, verses, time
            $table->string('target_type')->default('pages');

            // Daily target amount
            $table->integer('pages_per_day')->default(1);

            // Current position in the plan
            $table->integer('current_page')->default(1);

            // For custom plans: start and end pages
            $table->integer('start_page')->default(1);
            $table->integer('end_page')->default(604); // Total pages in Quran

            // Plan dates
            $table->date('start_date');
            $table->date('end_date')->nullable();

            // Reading intention/mode preference
            $table->enum('reading_mode', ['hadr', 'tadabbur'])->default('hadr');

            // Plan status
            $table->enum('status', ['active', 'paused', 'completed', 'abandoned'])->default('active');

            // JSON settings for theme, reciter preference, etc.
            $table->json('settings')->nullable();

            // Streak tracking
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('last_reading_date')->nullable();

            // Hatmah (completion) count
            $table->integer('hatmah_count')->default(0);

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index('last_reading_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_plans');
    }
};
