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
        Schema::create('system_performances', function (Blueprint $table) {
            $table->id();
            $table->float('cpu_usage')->comment('CPU usage percentage');
            $table->float('memory_usage')->comment('Memory usage percentage');
            $table->float('disk_usage')->comment('Disk usage percentage');
            $table->float('response_time')->comment('Response time in milliseconds');
            $table->boolean('has_alerts')->default(false);
            $table->json('alert_messages')->nullable();
            $table->timestamps();
            
            // Add index for faster queries on common filters
            $table->index('has_alerts');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_performances');
    }
};
