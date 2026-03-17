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
        Schema::create('invoices', function (Blueprint $table) {
           $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('collection_center_id')->constrained()->cascadeOnDelete(); 
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete(); 
            
            // Commission Links
            $table->foreignId('referred_by_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('referred_by_agent_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('invoice_number');
            $table->unique(['company_id', 'invoice_number']);
            $table->dateTime('invoice_date');

            // Pricing
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0); 
            
            // Commission Tracking
            $table->decimal('doctor_commission_amount', 10, 2)->default(0);
            $table->decimal('agent_commission_amount', 10, 2)->default(0);

            // Summary
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['Unpaid', 'Partial', 'Paid'])->default('Unpaid');
            
            $table->enum('status', ['Pending', 'Sample Collected', 'Processing', 'Completed', 'Cancelled'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
