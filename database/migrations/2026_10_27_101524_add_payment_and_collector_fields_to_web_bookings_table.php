<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_bookings', function (Blueprint $table) {
            $table->string('external_booking_reference', 50)->nullable()->after('booking_reference');
            $table->string('payment_method', 30)->nullable()->after('total_amount');
            $table->string('payment_status', 20)->default('pending')->after('payment_method');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('payment_status');
            $table->string('transaction_id', 100)->nullable()->after('paid_amount');
            $table->string('assigned_collector_name')->nullable()->after('source_ip');
            $table->string('assigned_collector_phone', 20)->nullable()->after('assigned_collector_name');
            $table->unsignedBigInteger('assigned_collector_id')->nullable()->after('assigned_collector_phone');
            $table->index(['company_id', 'external_booking_reference'], 'wb_ext_ref_idx');
        });
    }

    public function down(): void
    {
        Schema::table('web_bookings', function (Blueprint $table) {
            $table->dropIndex('wb_ext_ref_idx');
            $table->dropColumn([
                'external_booking_reference',
                'payment_method',
                'payment_status',
                'paid_amount',
                'transaction_id',
                'assigned_collector_name',
                'assigned_collector_phone',
                'assigned_collector_id',
            ]);
        });
    }
};
