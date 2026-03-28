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
        Schema::table('report_results', function (Blueprint $table) {
            $table->foreignId('invoice_item_id')->nullable()->after('lab_test_id')->constrained('invoice_items')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_results', function (Blueprint $table) {
            $table->dropForeign(['invoice_item_id']);
            $table->dropColumn('invoice_item_id');
        });
    }
};
