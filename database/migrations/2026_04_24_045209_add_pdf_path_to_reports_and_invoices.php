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
        Schema::table('test_reports', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('status');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_reports', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('pdf_path');
        });
    }
};
