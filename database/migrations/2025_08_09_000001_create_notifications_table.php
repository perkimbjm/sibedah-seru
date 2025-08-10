<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buat tabel notifications standar Laravel jika belum ada
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable'); // notifiable_type, notifiable_id
                $table->json('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Kembalikan ke kondisi sebelum migrasi (hapus tabel standar dan kembalikan legacy jika ada)
        if (Schema::hasTable('notifications')) {
            Schema::drop('notifications');
        }
    }
};


