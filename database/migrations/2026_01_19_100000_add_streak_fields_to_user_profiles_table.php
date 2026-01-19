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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->integer('current_streak')->default(0)->after('verses_memorized_count');
            $table->integer('best_streak')->default(0)->after('current_streak');
            $table->date('last_activity_date')->nullable()->after('best_streak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['current_streak', 'best_streak', 'last_activity_date']);
        });
    }
};
