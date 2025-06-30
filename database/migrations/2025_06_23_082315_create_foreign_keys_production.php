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
        // OBSOLETE: This migration is no longer needed
        // All foreign key constraints are now defined in their respective table creation migrations
        // Kept for migration history integrity
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to drop as this migration is obsolete
    }
};
