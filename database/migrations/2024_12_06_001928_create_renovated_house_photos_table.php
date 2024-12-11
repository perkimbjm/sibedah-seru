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

        Schema::create('renovated_house_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renovated_house_id')->constrained('houses')->cascadeOnDelete();
            $table->string('photo_url');
            $table->string('description')->nullable();
            $table->float('progres');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_best')->default(false);
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renovated_house_photos');
    }
};
