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
        Schema::table('applications', function (Blueprint $table) {
            $table->integer('score')->nullable()->after('processing_notes');
            $table->text('meeting_notes')->nullable()->after('score');

            // Update status enum to include new statuses
            $table->enum('status', ['pending', 'scored', 'meeting_scheduled', 'meeting_completed', 'approved', 'rejected'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['score', 'meeting_notes']);

            // Revert status enum to original values
            $table->enum('status', ['pending', 'partially approved', 'approved', 'rejected'])
                  ->default('pending')
                  ->change();
        });
    }
};
