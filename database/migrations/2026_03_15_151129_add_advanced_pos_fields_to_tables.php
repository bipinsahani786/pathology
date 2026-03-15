<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        // ==========================================
        // 1. INVOICES TABLE UPDATE
        // ==========================================
        Schema::table('invoices', function (Blueprint $table) {
            // Barcode for sample tracking
            $table->string('barcode')->unique()->nullable()->after('invoice_number');

            // Processing Branch (Lab) and Collection Type (Home/Center)
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete()->after('collection_center_id');
            $table->enum('collection_type', ['Center', 'Home Collection', 'Hospital'])->default('Center')->after('branch_id');

            // Expected Report Time
            $table->dateTime('expected_report_time')->nullable()->after('invoice_date');

            // Advanced Discount Tracking (Vouchers & Memberships)
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete()->after('agent_commission_amount');
            $table->decimal('membership_discount_amount', 10, 2)->default(0)->after('discount_amount');
            $table->decimal('voucher_discount_amount', 10, 2)->default(0)->after('membership_discount_amount');
        });

        // ==========================================
        // 2. PATIENT MEMBERSHIPS FIX
        // ==========================================
        Schema::table('patient_memberships', function (Blueprint $table) {
            // पुराने गलत Foreign Key को हटाना (जो lab_tests से जुड़ा था)
            $table->dropForeign(['membership_id']);

            // सही Foreign Key लगाना (जो memberships टेबल से जुड़ेगा)
            $table->foreign('membership_id')->references('id')->on('memberships')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Rollback Logic (अगर आप php artisan migrate:rollback करते हैं)
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['voucher_id']);
            $table->dropColumn([
                'barcode',
                'branch_id',
                'collection_type',
                'expected_report_time',
                'voucher_id',
                'membership_discount_amount',
                'voucher_discount_amount'
            ]);
        });

        Schema::table('patient_memberships', function (Blueprint $table) {
            $table->dropForeign(['membership_id']);
            $table->foreign('membership_id')->references('id')->on('lab_tests')->cascadeOnDelete();
        });
    }
};