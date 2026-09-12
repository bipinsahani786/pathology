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
        Schema::create('phlebotomist_commission_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phlebotomist_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->dateTime('payment_date');
            $table->string('payment_mode')->default('Cash'); // Cash, UPI, Bank Transfer, Cheque
            $table->string('reference_no')->nullable(); // UTR, Cheque No, Transaction ID
            $table->text('notes')->nullable();
            $table->foreignId('settled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('visits_count')->default(0);
            $table->timestamps();

            $table->index(['company_id', 'phlebotomist_id']);
        });

        Schema::table('home_collections', function (Blueprint $table) {
            $table->boolean('is_commission_settled')->default(false)->after('status');
            $table->decimal('commission_amount', 8, 2)->default(0)->after('is_commission_settled');
            $table->dateTime('commission_settled_at')->nullable()->after('commission_amount');
            $table->foreignId('commission_settlement_id')->nullable()->after('commission_settled_at')->constrained('phlebotomist_commission_settlements')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_collections', function (Blueprint $table) {
            $table->dropForeign(['commission_settlement_id']);
            $table->dropColumn([
                'is_commission_settled',
                'commission_amount',
                'commission_settled_at',
                'commission_settlement_id',
            ]);
        });

        Schema::dropIfExists('phlebotomist_commission_settlements');
    }
};
