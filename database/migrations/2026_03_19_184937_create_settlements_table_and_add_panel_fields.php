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
        // 1. Create Settlements Table
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Doctor/Agent/CC User
            $table->decimal('amount', 12, 2);
            $table->dateTime('payment_date');
            $table->string('payment_mode'); // Cash, UPI, Bank Transfer
            $table->string('reference_no')->nullable(); // UTR, Check No
            $table->enum('type', ['Doctor', 'Agent', 'CollectionCenter']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Add collection_center_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('collection_center_id')->nullable()->after('branch_id')->constrained('collection_centers')->nullOnDelete();
        });

        // 3. Add settlement links to invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('doctor_settlement_id')->nullable()->after('referred_by_doctor_id')->constrained('settlements')->nullOnDelete();
            $table->foreignId('agent_settlement_id')->nullable()->after('referred_by_agent_id')->constrained('settlements')->nullOnDelete();
            $table->foreignId('cc_settlement_id')->nullable()->after('collection_center_id')->constrained('settlements')->nullOnDelete();
            
            // Helpful flags for quick filtering
            $table->boolean('is_doctor_settled')->default(false);
            $table->boolean('is_agent_settled')->default(false);
            $table->boolean('is_cc_settled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['doctor_settlement_id']);
            $table->dropForeign(['agent_settlement_id']);
            $table->dropForeign(['cc_settlement_id']);
            $table->dropColumn(['doctor_settlement_id', 'agent_settlement_id', 'cc_settlement_id', 'is_doctor_settled', 'is_agent_settled', 'is_cc_settled']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['collection_center_id']);
            $table->dropColumn('collection_center_id');
        });

        Schema::dropIfExists('settlements');
    }
};
