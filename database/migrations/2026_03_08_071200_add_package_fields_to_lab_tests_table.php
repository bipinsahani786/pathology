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
            // Indicates whether this record is a single test or a package/profile
            $table->boolean('is_package')->default(false)->after('department');
            
            // Stores the IDs of the single tests included in this package as a JSON array (e.g., [1, 4, 10])
            $table->json('linked_test_ids')->nullable()->after('is_package');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_tests', function (Blueprint $table) {
            $table->dropColumn(['is_package', 'linked_test_ids']);
        });
    }
};
