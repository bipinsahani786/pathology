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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete(); // Multi-tenancy
            
            $table->string('name'); // e.g., Gold Health Card, Senior Citizen
            $table->decimal('price', 10, 2)->default(0); // Cost to buy this membership (₹)
            $table->decimal('discount_percentage', 5, 2)->default(0); // Flat % discount on all tests
            $table->integer('validity_days')->default(365); // e.g., 365 for 1 year
            $table->string('color_code', 20)->default('#3b71ca'); // For beautiful UI cards
            $table->text('description')->nullable(); // Benefits details
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->index(['company_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
