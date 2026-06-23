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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn([
                'is_outsourced',
                'outsourced_pdf_path',
                'outsourced_lab_name',
                'outsourced_crop_top',
                'outsourced_crop_bottom',
            ]);
        });

        Schema::table('test_reports', function (Blueprint $table) {
            $table->string('outsourced_pdf_path')->nullable()->after('pdf_path');
            $table->string('outsourced_lab_name')->nullable()->after('outsourced_pdf_path');
            $table->integer('outsourced_crop_top')->default(18)->after('outsourced_lab_name');
            $table->integer('outsourced_crop_bottom')->default(5)->after('outsourced_crop_top');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_reports', function (Blueprint $table) {
            $table->dropColumn([
                'outsourced_pdf_path',
                'outsourced_lab_name',
                'outsourced_crop_top',
                'outsourced_crop_bottom',
            ]);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->boolean('is_outsourced')->default(false)->after('is_package');
            $table->string('outsourced_pdf_path')->nullable()->after('is_outsourced');
            $table->string('outsourced_lab_name')->nullable()->after('outsourced_pdf_path');
            $table->integer('outsourced_crop_top')->default(18)->after('outsourced_lab_name');
            $table->integer('outsourced_crop_bottom')->default(5)->after('outsourced_crop_top');
        });
    }
};
