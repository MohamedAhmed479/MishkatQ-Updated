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
        Schema::create('juzs', function (Blueprint $table) {
            $table->id(); // المفتاح الأساسي
            $table->unsignedInteger('juz_number')->unique(); // رقم الجزء، فريد

            $table->unsignedBigInteger('start_verse_id');
            $table->foreign("start_verse_id")->references("id")->on("verses")->onDelete("cascade");
            $table->unsignedBigInteger('end_verse_id');
            $table->foreign("end_verse_id")->references("id")->on("verses")->onDelete("cascade");

            $table->integer('verses_count');

            // Indexes
            $table->index('juz_number');
            $table->index('start_verse_id');
            $table->index('end_verse_id');
            $table->index('verses_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juzs');
    }
};
