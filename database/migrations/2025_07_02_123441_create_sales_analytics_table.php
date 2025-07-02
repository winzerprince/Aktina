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
        Schema::create('sales_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('revenue', 15, 2)->default(0);
            $table->integer('orders_count')->default(0);
            $table->integer('customers_count')->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->integer('products_sold')->default(0);
            $table->decimal('profit_margin', 5, 2)->default(0); // Percentage
            $table->integer('new_customers')->default(0);
            $table->integer('returning_customers')->default(0);
            $table->json('top_products')->nullable(); // JSON array of best selling products
            $table->json('sales_by_category')->nullable(); // JSON breakdown by product category
            $table->timestamps();

            $table->unique(['date', 'user_id']);
            $table->index(['date', 'revenue']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_analytics');
    }
};
