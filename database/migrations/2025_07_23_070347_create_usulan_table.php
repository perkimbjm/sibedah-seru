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
        Schema::create('usulan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama', 255);
            $table->string('nik', 16)->unique();
            $table->string('nomor_kk', 16)->unique();
            $table->string('nomor_hp', 15)->nullable();
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('village_id');
            $table->text('alamat_lengkap');
            $table->enum('jenis_usulan', ['RTLH', 'Rumah Korban Bencana']);
            $table->string('foto_rumah', 255)->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->enum('status', ['pending', 'verified', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->foreign('village_id')->references('id')->on('villages')->onDelete('cascade');

            // Indexes for better performance
            $table->index(['status']);
            $table->index(['user_id']);
            $table->index(['district_id']);
            $table->index(['village_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usulan');
    }
};
