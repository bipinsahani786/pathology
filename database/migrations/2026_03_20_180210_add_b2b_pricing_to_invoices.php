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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('total_b2b_amount', 10, 2)->default(0)->after('total_amount')->comment('Sum of B2B prices for all items');
            $table->decimal('cc_profit_amount', 10, 2)->default(0)->after('total_b2b_amount')->comment('Retail Total - B2B Total');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('b2b_price', 10, 2)->default(0)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['total_b2b_amount', 'cc_profit_amount']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('b2b_price');
        });
    }
};
