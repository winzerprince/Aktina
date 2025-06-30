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
        Schema::create('retailers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('male_female_ratio', 5, 2)->nullable(); // Ratio of male to female customers
            $table->string('city')->nullable();
            $table->enum('urban_rural_classification', ['urban', 'suburban', 'rural'])->nullable();
            $table->enum('customer_age_class', ['child', 'teenager', 'youth', 'adult', 'senior'])->nullable();
            $table->enum('customer_income_bracket', ['low', 'medium', 'high'])->nullable();
            $table->enum('customer_education_level', ['low', 'mid', 'high'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retailers');
    }
};
