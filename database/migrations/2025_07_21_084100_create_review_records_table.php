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
        Schema::create('review_records', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('spaced_repetition_id');
            $table->foreign("spaced_repetition_id")->references("id")->on("spaced_repetitions")->onDelete("cascade");

            $table->timestamp('review_date');
            $table->tinyInteger('performance_rating'); // 0–5
            $table->boolean('successful');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_records');
    }
};
