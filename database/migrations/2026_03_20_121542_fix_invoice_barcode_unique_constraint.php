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
            // Drop global unique constraint
            $table->dropUnique(['barcode']);
            
            // Add composite unique constraint per company
            $table->unique(['company_id', 'barcode']);
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'barcode']);
            $table->unique('barcode');
        });
    }
};
