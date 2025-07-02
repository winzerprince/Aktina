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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Short code like 'WH-01', 'WH-02'
            $table->enum('type', ['main', 'component', 'finished_goods', 'returns']);
            $table->text('location')->nullable();
            $table->json('address')->nullable(); // JSON for structured address data
            $table->integer('total_capacity')->default(0); // Maximum items it can hold
            $table->integer('current_usage')->default(0); // Current items stored
            $table->decimal('capacity_utilization', 5, 2)->default(0); // Percentage
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index('current_usage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
