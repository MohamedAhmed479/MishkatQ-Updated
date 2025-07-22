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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete("cascade");

            $table->unsignedBigInteger('tafsir_id')->nullable();
            $table->foreign('tafsir_id')->references('id')->on('tafsirs')->onDelete("set null");

            $table->integer('daily_minutes');
            $table->integer('sessions_per_day');
            $table->text('preferred_times')->default(json_encode([]));
            $table->enum("current_level", ["beginner", "intermediate", "advanced"]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
