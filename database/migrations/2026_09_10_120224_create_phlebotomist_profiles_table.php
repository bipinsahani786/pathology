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
        Schema::create('phlebotomist_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('vehicle_number')->nullable();
            $table->enum('vehicle_type', ['Bike', 'Car', 'Scooty', 'Other'])->nullable();
            $table->decimal('commission_per_visit', 8, 2)->default(0);
            $table->boolean('is_available')->default(true); // Admin manually toggle (e.g., leave day)
            $table->time('working_hours_start')->nullable();  // e.g., 08:00
            $table->time('working_hours_end')->nullable();    // e.g., 18:00
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phlebotomist_profiles');
    }
};
