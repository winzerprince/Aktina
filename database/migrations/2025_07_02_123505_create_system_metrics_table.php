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
        Schema::create('system_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('active_users')->default(0);
            $table->integer('new_users')->default(0);
            $table->integer('total_orders')->default(0);
            $table->integer('pending_orders')->default(0);
            $table->integer('completed_orders')->default(0);
            $table->integer('cancelled_orders')->default(0);
            $table->decimal('system_uptime', 5, 2)->default(100); // Percentage
            $table->integer('api_requests')->default(0);
            $table->decimal('average_response_time', 8, 2)->default(0); // Milliseconds
            $table->integer('error_count')->default(0);
            $table->integer('login_count')->default(0);
            $table->integer('messages_sent')->default(0);
            $table->integer('files_uploaded')->default(0);
            $table->integer('inventory_movements')->default(0);
            $table->json('performance_data')->nullable(); // JSON of detailed performance metrics
            $table->timestamps();

            $table->unique('date');
            $table->index(['date', 'active_users']);
            $table->index('total_orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_metrics');
    }
};
