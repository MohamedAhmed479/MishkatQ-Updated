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
        Schema::create('spaced_repetitions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('plan_item_id');
            $table->foreign("plan_item_id")->references("id")->on("plan_items")->onDelete("cascade");

            $table->integer('interval_index'); // 0 = initial, 1 = 1 day, ...
            $table->date('scheduled_date');
            $table->float('ease_factor');
            $table->integer('repetition_count');
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamps(); // includes created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spaced_repetitions');
    }
};
