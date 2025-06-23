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
        Schema::create('application', function (Blueprint $table) {
            $table->id()->primary();
            $table->enum('status', ['pending','partially approved', 'approved', 'rejected'])->default('pending');
            $table->date('meeting_schedule')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable(); // Add vendor_id field
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application');
    }
};
