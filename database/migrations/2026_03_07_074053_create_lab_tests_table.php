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
        Schema::create('lab_tests', function (Blueprint $table) {
           $table->id();
            
            // Link to the specific Lab (Tenant)
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            
            // Link to Global Test (null means it's a custom test made by the lab)
            $table->foreignId('global_test_id')->nullable()->constrained('global_tests')->nullOnDelete();

            $table->string('test_code', 50)->nullable(); // e.g., LIPID-01
            $table->string('name');
            $table->string('department')->nullable(); 

            // Pricing 
            $table->decimal('mrp', 10, 2)->default(0); 
            $table->decimal('b2b_price', 10, 2)->default(0); 

            // Requirements
            $table->string('sample_type')->nullable(); 
            $table->integer('tat_hours')->default(24); 
            
            // The JSON array containing parameters & formulas
            $table->jsonb('parameters')->nullable(); 

            $table->boolean('is_active')->default(true);

            $table->text('description')->nullable(); 
            $table->timestamps();

            $table->index(['company_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
