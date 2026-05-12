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
        Schema::create('landing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('price_text')->nullable(); // e.g. 'Free', '₹999/month', 'Custom'
            $table->string('badge')->nullable(); // e.g. 'Most Popular'
            $table->json('features')->nullable(); // List of features
            $table->string('cta_text')->nullable();
            $table->string('cta_link')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_plans');
    }
};
