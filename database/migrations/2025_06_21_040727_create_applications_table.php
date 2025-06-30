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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending','partially approved', 'approved', 'rejected'])->default('pending');
            $table->date('meeting_schedule')->nullable();
            $table->foreignId('vendor_id')->unique()->constrained('vendors')->onDelete('cascade')->nullable(); // One-to-one with vendor
            $table->string('pdf_path')->nullable(); // Path to the stored PDF file
            $table->json('form_data')->nullable(); // Structured form data extracted from PDF
            $table->boolean('processed_by_java_server')->default(false); // Flag for Java server processing
            $table->timestamp('processing_date')->nullable(); // When the application was processed
            $table->text('processing_notes')->nullable(); // Notes from the processing
            $table->string('application_reference')->nullable()->unique(); // Unique reference number
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
