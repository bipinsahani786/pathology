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
            $table->text('culture_data')->nullable(); // Using text for safe cross-DB compatibility (Sqlite/MySQL/PostgreSQL)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_results', function (Blueprint $table) {
            $table->dropColumn('culture_data');
        });
    }
};
