<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update enum values for status
        DB::statement("ALTER TABLE complaints MODIFY COLUMN status 
        ENUM('pending', 'in_progress', 'resolved', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE complaints MODIFY COLUMN status 
        ENUM('dikirim', 'diproses', 'ditolak', 'selesai') DEFAULT 'dikirim'");
    }
};