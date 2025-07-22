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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // User information
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type', 50)->nullable(); // User, Admin, System
            $table->string('user_name', 100)->nullable();
            $table->string('user_email', 100)->nullable();

            // Action details
            $table->string('action', 50); // created, updated, deleted, login, logout, etc.
            $table->string('description', 500)->nullable();

            // Model/Resource information
            $table->string('model_type', 100)->nullable(); // User, MemorizationPlan, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_name', 100)->nullable();

            // Change tracking
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Request information
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('method', 10)->nullable(); // GET, POST, PUT, DELETE
            $table->string('url', 500)->nullable();
            $table->json('request_data')->nullable();

            // Security and context
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('is_sensitive')->default(false);
            $table->string('session_id', 100)->nullable();
            $table->string('device_info', 200)->nullable();

            // Status and categorization
            $table->enum('status', ['success', 'failed', 'warning'])->default('success');
            $table->string('category', 50)->nullable(); // auth, data, admin, security
            $table->json('metadata')->nullable(); // Additional context data

            $table->timestamp('performed_at');
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'performed_at']);
            $table->index(['action', 'performed_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['category', 'performed_at']);
            $table->index(['severity', 'performed_at']);
            $table->index(['ip_address', 'performed_at']);
            $table->index('performed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
