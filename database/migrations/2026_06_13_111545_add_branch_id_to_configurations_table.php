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
        Schema::table('configurations', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->cascadeOnDelete();
            $table->dropUnique(['company_id', 'config_key']);
            $table->unique(['company_id', 'branch_id', 'config_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'branch_id', 'config_key']);
            $table->dropColumn('branch_id');
            $table->unique(['company_id', 'config_key']);
        });
    }
};
