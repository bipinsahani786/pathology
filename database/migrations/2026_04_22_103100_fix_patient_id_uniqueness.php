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
        Schema::table('patient_profiles', function (Blueprint $table) {
            // Drop the global unique constraint
            $table->dropUnique(['patient_id_string']);
            
            // Create a company-scoped unique constraint
            $table->unique(['company_id', 'patient_id_string']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'patient_id_string']);
            $table->unique('patient_id_string');
        });
    }
};
