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
        Schema::create('verses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('chapter_id');
            $table->foreign("chapter_id")->references("id")->on("chapters")->onDelete("cascade");

            $table->unsignedInteger('verse_number');
            $table->string('verse_key', 10)->nullable(); // مثال: 2:255
            $table->text('text_uthmani');
            $table->text('text_imlaei')->nullable();
            $table->unsignedInteger('page_number')->nullable();
            $table->unsignedInteger('juz_number')->nullable();
            $table->unsignedInteger('hizb_number')->nullable();
            $table->unsignedInteger('rub_number')->nullable();
            $table->boolean('sajda')->default(false);

            // Indexes
            $table->index('chapter_id');
            $table->index(['chapter_id', 'verse_number']); // مركب للبحث عن آية في سورة
            $table->index('verse_key');
            $table->index('page_number');
            $table->index('juz_number');
            $table->index('hizb_number');
            $table->index('rub_number');
            $table->index('sajda');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verses');
    }
};
