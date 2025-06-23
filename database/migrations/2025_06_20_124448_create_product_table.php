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
        Schema::create('product', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
            $table->string('model'); // e.g., "Aktina Pro 15", "Aktina Lite 12"
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->decimal('msrp', 10, 2)->nullable();
            $table->string('category')->default('smartphone'); // smartphone, tablet, etc.
            $table->json('specifications')->nullable(); // Display size, RAM, storage, etc.
            $table->string('target_market')->nullable(); // flagship, mid-range, budget
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
