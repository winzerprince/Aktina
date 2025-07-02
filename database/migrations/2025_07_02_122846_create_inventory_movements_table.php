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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained('resources')->onDelete('cascade');
            $table->foreignId('from_warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->foreignId('to_warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->enum('movement_type', ['inbound', 'outbound', 'transfer', 'adjustment', 'production_use', 'return']);
            $table->integer('quantity');
            $table->integer('before_quantity');
            $table->integer('after_quantity');
            $table->string('reference_number')->nullable(); // Order number, transfer ID, etc.
            $table->foreignId('moved_by')->constrained('users')->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable(); // Additional context like order details
            $table->timestamps();

            $table->index(['resource_id', 'movement_type']);
            $table->index(['from_warehouse_id', 'to_warehouse_id']);
            $table->index('moved_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
