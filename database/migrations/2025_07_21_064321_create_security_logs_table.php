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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // login_failed, register_failed, bruteforce, etc.
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->string('email')->nullable();
            $table->json('details')->nullable(); // Additional data
            $table->enum('status', ['success', 'failed', 'blocked', 'info'])->default('info');
            $table->integer('attempt_count')->default(1);
            $table->timestamps();

            // Indexes for better performance
            $table->index(['ip_address', 'event_type']);
            $table->index(['created_at']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
