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
        // This migration is no longer needed as foreign keys are now defined in the individual table migrations
        // All foreign key constraints have been moved to their respective table creation migrations
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to drop as this migration now does nothing
    }
};
