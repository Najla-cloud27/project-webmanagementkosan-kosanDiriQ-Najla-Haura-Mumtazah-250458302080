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
        Schema::create('announcoments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->
            constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('publish_status', 
            ['draf', 'diterbitkan'])->default('draf');
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcoments');
    }
};