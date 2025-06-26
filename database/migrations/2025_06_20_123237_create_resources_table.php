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
        Schema::create('resources', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
            $table->string('component_type'); // SoC, Display, Camera, Battery, etc.
            $table->string('category'); // Core Processing, Display Systems, Camera Systems, etc.
            $table->integer('units')->default(0);
            $table->integer('reorder_level')->default(100);
            $table->integer('overstock_level')->default(1000);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('part_number')->nullable();
            $table->json('specifications')->nullable(); // JSON for technical specs
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
