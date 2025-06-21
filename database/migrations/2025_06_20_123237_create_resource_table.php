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
        Schema::create('resource', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('name');
            $table->integer('units')->default(0);
            $table->integer('reorder_level')->default(100);
            $table->integer('overstock_level')->default(1000);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource');
    }
};
