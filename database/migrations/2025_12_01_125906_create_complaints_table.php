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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->
            constrained('users')->onDelete('cascade');
            $table->foreignId('room_id')->
            constrained('rooms')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('image_url')->nullable();
            $table->enum('status', ['dikirim', 'diproses', 
            'ditolak', 'selesai'])->default('dikirim');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};