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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->
            constrained('users')->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->
            constrained('bookings')->onDelete('cascade');
            $table->string('bill_code')->unique();
            $table->text('description')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->date('due_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('status', ['belum_dibayar', 'menunggu_verifikasi', 'dibayar', 'overdue'])->
            default('belum_dibayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};