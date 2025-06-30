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
        Schema::create('resource_orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 10, 2);
            $table->json('items'); // JSON field to store resource IDs and quantities
            $table->enum('status', ['pending', 'accepted', 'complete'])->default('pending');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade'); // Aktina as buyer
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade'); // Supplier as seller
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_orders');
    }
};
