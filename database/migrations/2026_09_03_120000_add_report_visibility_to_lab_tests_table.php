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
        Schema::table('lab_tests', function (Blueprint $table) {
            $table->boolean('show_method_on_report')->default(true)->after('interpretation');
            $table->boolean('show_interpretation_on_report')->default(true)->after('show_method_on_report');
            $table->boolean('show_note_on_report')->default(true)->after('show_interpretation_on_report');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->jsonb('report_options')->nullable()->after('report_comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_tests', function (Blueprint $table) {
            $table->dropColumn([
                'show_method_on_report',
                'show_interpretation_on_report',
                'show_note_on_report',
            ]);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('report_options');
        });
    }
};
