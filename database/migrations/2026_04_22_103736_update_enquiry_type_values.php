<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For Postgres, we need to drop the check constraint and add a new one
        // Laravel's enum() on Postgres creates a CHECK constraint
        DB::statement("ALTER TABLE enquiries DROP CONSTRAINT IF EXISTS enquiries_enquiry_type_check");
        
        // Change the column to include 'website'
        // We use a raw statement to ensure it works on Postgres
        DB::statement("ALTER TABLE enquiries ADD CONSTRAINT enquiries_enquiry_type_check CHECK (enquiry_type IN ('contact', 'enquiry', 'demo_request', 'website'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE enquiries DROP CONSTRAINT IF EXISTS enquiries_enquiry_type_check");
        DB::statement("ALTER TABLE enquiries ADD CONSTRAINT enquiries_enquiry_type_check CHECK (enquiry_type IN ('contact', 'enquiry', 'demo_request'))");
    }
};
