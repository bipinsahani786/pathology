<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('global_tests', function (Blueprint $table) {
            $table->id();
            $table->string('test_code')->unique(); // e.g., 'CBC-01'
            $table->string('name'); // e.g., 'Complete Blood Count'
            $table->string('category'); // e.g., 'Haematology', 'Biochemistry'
            $table->text('description')->nullable();
            // Default parameters for the test, stored as JSON. This can include reference ranges, units, etc.
            // like: [{"param": "Hemoglobin", "unit": "g/dL", "male_range": "13-17"}]
            $table->jsonb('default_parameters')->nullable();

            $table->decimal('suggested_price', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_tests');
    }
};
