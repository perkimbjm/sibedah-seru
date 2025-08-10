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
        Schema::create('aduan', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('contact')->nullable();

            // Foreign keys untuk district dan village
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('village_id');

            // Pengaduan bertahap
            $table->text('complain');
            $table->text('complain2')->nullable();
            $table->text('complain3')->nullable();

            // Timestamps untuk complain lanjutan
            $table->timestamp('complain2_at')->nullable();
            $table->timestamp('complain3_at')->nullable();

            // Respon admin bertahap
            $table->text('respon')->nullable();
            $table->text('respon2')->nullable();
            $table->text('respon3')->nullable();

            // Timestamps untuk respon
            $table->timestamp('respon_at')->nullable();
            $table->timestamp('respon2_at')->nullable();
            $table->timestamp('respon3_at')->nullable();

            $table->string('foto', 255)->nullable()->comment('Path file foto pengaduan');

            // Polling evaluasi (1-4)
            $table->tinyInteger('expect')->nullable();

            // Kode tiket unik
            $table->string('kode_tiket', 20)->unique();

            // User ID untuk yang login (nullable)
            $table->unsignedBigInteger('user_id')->nullable();

            // Status pengaduan
            $table->enum('status', ['pending', 'in_progress', 'completed', 'closed'])->default('pending');

            // Timestamps
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->foreign('village_id')->references('id')->on('villages')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['kode_tiket']);
            $table->index(['status']);
            $table->index(['district_id']);
            $table->index(['village_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aduan');
    }
};
