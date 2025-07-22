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
        Schema::create('plan_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('plan_id');
            $table->foreign("plan_id")->references("id")->on("memorization_plans")->onDelete("cascade");

            $table->unsignedBigInteger('quran_surah_id');
            $table->foreign("quran_surah_id")->references("id")->on("chapters")->onDelete("cascade");

            $table->unsignedBigInteger('verse_start_id');
            $table->foreign("verse_start_id")->references("id")->on("verses")->onDelete("cascade");

            $table->unsignedBigInteger('verse_end_id');
            $table->foreign("verse_end_id")->references("id")->on("verses")->onDelete("cascade");

            $table->boolean("is_completed")->default(false);

            $table->date('target_date');
            $table->integer('sequence')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_items');
    }
};
