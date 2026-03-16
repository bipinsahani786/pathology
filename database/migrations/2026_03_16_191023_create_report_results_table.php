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
        Schema::create('report_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_test_id')->constrained('lab_tests')->cascadeOnDelete();
            
            $table->string('parameter_name');
            $table->string('short_code')->nullable();
            $table->string('result_value')->nullable();
            
            $table->enum('status', ['Normal', 'High', 'Low'])->default('Normal');
            $table->boolean('is_highlighted')->default(false);
            
            $table->string('reference_range')->nullable();
            $table->string('unit')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_results');
    }
};
