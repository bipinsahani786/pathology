<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix: membership_id FK was pointing to lab_tests, should point to memberships.
     */
    public function up(): void
    {
        Schema::table('patient_memberships', function (Blueprint $table) {
            // Drop the old wrong FK
            $table->dropForeign(['membership_id']);
            
            // Re-add correct FK pointing to memberships table
            $table->foreign('membership_id')->references('id')->on('memberships')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('patient_memberships', function (Blueprint $table) {
            $table->dropForeign(['membership_id']);
            $table->foreign('membership_id')->references('id')->on('lab_tests')->cascadeOnDelete();
        });
    }
};
