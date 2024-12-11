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
        Schema::disableForeignKeyConstraints();

        Schema::create('house_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained('rtlh')->cascadeOnDelete();
            $table->string('photo_url');
            $table->string('description')->nullable();
            $table->foreignId('rtlh_id');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('house_photos');
    }
};
