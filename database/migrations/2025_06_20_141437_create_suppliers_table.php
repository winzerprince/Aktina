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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id()->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('company_name');
            $table->string('region'); // Asia-Pacific, Europe, US, etc.
            $table->json('component_categories')->nullable(); // Categories they supply
            $table->decimal('reliability_rating', 3, 2)->default(5.00); // Out of 5
            $table->boolean('is_preferred')->default(false);
            $table->text('certifications')->nullable(); // ISO, conflict-free, etc.
            $table->json('resources')->nullable(); // Keep for backward compatibility
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
