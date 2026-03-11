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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete(); // Multi-tenancy
            
            $table->string('code')->index(); // e.g., DIWALI50, HEALTH26 (Unique per company)
            $table->enum('discount_type', ['percentage', 'flat']); // Type of discount
            $table->decimal('discount_value', 10, 2); // 50 (₹) or 10 (%)
            
            // Professional Business Rules
            $table->decimal('min_bill_amount', 10, 2)->default(0); // Minimum cart value to apply
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // Cap for percentage discount
            
            // Validity & Limits
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->integer('usage_limit')->nullable(); // Total times this code can be used in the lab
            $table->integer('used_count')->default(0); // How many times it has been used
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // A company cannot have two active vouchers with the exact same code
            $table->unique(['company_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
