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
        Schema::create('daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('role'); // admin, vendor, retailer, etc.
            $table->string('metric_type'); // sales, inventory, users, etc.
            $table->string('metric_name'); // revenue, orders_count, stock_level, etc.
            $table->decimal('value', 15, 2);
            $table->string('unit')->nullable(); // currency, count, percentage, etc.
            $table->json('metadata')->nullable(); // Additional context
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['date', 'role', 'metric_type', 'metric_name', 'user_id'], 'daily_metrics_unique');
            $table->index(['date', 'role']);
            $table->index(['metric_type', 'metric_name']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_metrics');
    }
};
