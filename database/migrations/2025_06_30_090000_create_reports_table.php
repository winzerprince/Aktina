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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type'); // sales, inventory, production, etc.
            $table->text('description')->nullable();
            $table->json('data'); // Stores the report data in JSON format
            $table->foreignId('generated_by')->nullable();
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('set null');
            $table->dateTime('generated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
