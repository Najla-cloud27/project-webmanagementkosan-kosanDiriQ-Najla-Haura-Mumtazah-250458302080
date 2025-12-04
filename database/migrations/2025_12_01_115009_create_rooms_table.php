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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('size', 50)->nullable();
            $table->enum('status', ['tersedia', 'terisi', 
            'perawatan', 'sudah_dipesan'])->default('tersedia');
            $table->text('fasilitas')->nullable();
            $table->integer('stok')->default(0);
            $table->string('main_image_url')->nullable();
            $table->text('icon_svg')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};