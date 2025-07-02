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
        Schema::create('production_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('efficiency_rate', 5, 2)->default(0); // Percentage
            $table->decimal('fulfillment_rate', 5, 2)->default(0); // Percentage  
            $table->decimal('resource_usage', 5, 2)->default(0); // Percentage of capacity used
            $table->integer('units_produced')->default(0);
            $table->integer('units_planned')->default(0);
            $table->decimal('waste_percentage', 5, 2)->default(0);
            $table->integer('defect_count')->default(0);
            $table->decimal('quality_score', 5, 2)->default(0); // Percentage
            $table->integer('downtime_minutes')->default(0);
            $table->decimal('cost_per_unit', 10, 2)->default(0);
            $table->json('resource_breakdown')->nullable(); // JSON of resource consumption
            $table->json('production_lines')->nullable(); // JSON of production line performance
            $table->timestamps();

            $table->unique('date');
            $table->index(['date', 'efficiency_rate']);
            $table->index('fulfillment_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_metrics');
    }
};
