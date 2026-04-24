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
        Schema::create('sales_agent_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_agent_id')->constrained('sales_agents')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->timestamp('paid_at');
            $table->string('payment_method')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_agent_payouts');
    }
};
