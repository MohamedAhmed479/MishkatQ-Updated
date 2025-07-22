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
        Schema::create('recitations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('verse_id');
            $table->foreign("verse_id")->references("id")->on("verses")->onDelete("cascade");

            $table->unsignedBigInteger('reciter_id');
            $table->foreign("reciter_id")->references("id")->on("reciters")->onDelete("cascade");

            $table->string('audio_url', 100)->nullable();

            // Indexes
            $table->index('verse_id');
            $table->index('reciter_id');
            $table->unique(['verse_id', 'reciter_id']); // لضمان عدم تكرار نفس الآية لنفس القارئ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recitations');
    }
};
