<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            // Tambahkan foreign key constraint
            $table->foreign('district_id')
                  ->references('id')
                  ->on('districts')
                  ->onDelete('cascade'); // hapus village jika district dihapus
                  // Atau gunakan ->onDelete('set null') jika ingin set null saat district dihapus
        });
    }

    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            // Hapus foreign key constraint
            $table->dropForeign(['district_id']);
        });
    }
};