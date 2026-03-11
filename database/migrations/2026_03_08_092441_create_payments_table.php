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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            
            $table->foreignId('collected_by')->constrained('users')->cascadeOnDelete(); 
            $table->foreignId('payment_mode_id')->constrained('payment_modes')->cascadeOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('transaction_id')->nullable(); 
            $table->text('remarks')->nullable(); // e.g., Advance, Balance clearance
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
