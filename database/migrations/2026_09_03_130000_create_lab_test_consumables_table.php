<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mapping table: which inventory items a lab test consumes
        Schema::create('lab_test_consumables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_test_id')->constrained('lab_tests')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('quantity_per_test', 15, 2)->default(1);
            $table->timestamps();

            $table->unique(['lab_test_id', 'inventory_item_id'], 'ltc_test_item_unique');
        });

        // Flag to prevent double inventory deduction on re-approval
        Schema::table('test_reports', function (Blueprint $table) {
            $table->boolean('inventory_deducted')->default(false)->after('report_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_test_consumables');

        Schema::table('test_reports', function (Blueprint $table) {
            $table->dropColumn('inventory_deducted');
        });
    }
};
