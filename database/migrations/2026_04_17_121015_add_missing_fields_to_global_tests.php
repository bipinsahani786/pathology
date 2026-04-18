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
        Schema::table('global_tests', function (Blueprint $table) {
            $table->renameColumn('suggested_price', 'mrp');
            $table->decimal('b2b_price', 10, 2)->nullable()->after('suggested_price');
            $table->string('sample_type', 100)->nullable()->after('name');
            $table->integer('tat_hours')->default(24)->after('sample_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_tests', function (Blueprint $table) {
            $table->renameColumn('mrp', 'suggested_price');
            $table->dropColumn(['b2b_price', 'sample_type', 'tat_hours']);
        });
    }
};
