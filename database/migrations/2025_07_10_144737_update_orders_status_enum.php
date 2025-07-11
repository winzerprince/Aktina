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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
                'processing',
                'partially_fulfilled',
                'fulfilled',
                'shipped',
                'in_transit',
                'delivered',
                'complete',
                'cancelled',
                'returned',
                'fulfillment_failed'
            ])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'complete'])->default('pending')->change();
        });
    }
};
