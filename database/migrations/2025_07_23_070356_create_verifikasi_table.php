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
        Schema::create('verifikasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('usulan_id');
            $table->unsignedBigInteger('verifikator_id');
            $table->boolean('kesesuaian_tata_ruang')->nullable();
            $table->boolean('tidak_dalam_sengketa')->nullable();
            $table->boolean('memiliki_alas_hak')->nullable();
            $table->boolean('satu_satunya_rumah')->nullable();
            $table->boolean('belum_pernah_bantuan')->nullable();
            $table->boolean('berpenghasilan_rendah')->nullable();
            $table->enum('hasil_verifikasi', ['diterima', 'belum_memenuhi_syarat']);
            $table->text('catatan_verifikator')->nullable();
            $table->timestamp('verified_at')->useCurrent();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('usulan_id')->references('id')->on('usulan')->onDelete('cascade');
            $table->foreign('verifikator_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes for better performance
            $table->index(['usulan_id']);
            $table->index(['verifikator_id']);
            $table->index(['hasil_verifikasi']);
            $table->index(['verified_at']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikasi');
    }
};
