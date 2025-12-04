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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->
            onDelete('cascade');
            $table->foreignId('room_id')->constrained('rooms')->
            onDelete('cascade');
            $table->string('booking_code')->unique();
            $table->integer('duration_in_months')->default(1);
            $table->timestamp('selesai_booking')->nullable();
            $table->timestamp('planned_check_in_date')->nullable();
            $table->decimal('total_price', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 
            'menunggu_verifikasi', 'dikonfirmasi', 
            'selesai', 'dibatalkan'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};