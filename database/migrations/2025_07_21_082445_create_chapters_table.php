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
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();

            $table->string('name_ar', 100);
            $table->string('name_en', 100)->nullable();
            $table->enum('revelation_place', ['makkah', 'madinah']);
            $table->unsignedInteger('revelation_order')->nullable();
            $table->unsignedInteger('verses_count')->nullable();

            // Indexes
            $table->index('name_ar');
            $table->index('name_en');
            $table->index('revelation_place');
            $table->index('revelation_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
