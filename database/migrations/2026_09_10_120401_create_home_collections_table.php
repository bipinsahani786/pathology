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
        Schema::create('home_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('phlebotomist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();

            // Address & Location
            $table->text('collection_address');
            $table->string('collection_landmark')->nullable();
            $table->decimal('collection_lat', 10, 7)->nullable();  // GPS latitude
            $table->decimal('collection_lng', 10, 7)->nullable();  // GPS longitude

            // Schedule
            $table->date('scheduled_date');
            $table->time('scheduled_slot_start')->nullable(); // e.g., 09:00
            $table->time('scheduled_slot_end')->nullable();   // e.g., 10:00

            // Status Lifecycle Timestamps
            $table->dateTime('assigned_at')->nullable();
            $table->dateTime('en_route_at')->nullable();   // Phlebotomist ne nikal liya
            $table->dateTime('arrived_at')->nullable();    // Patient ke ghar pahunch gaya
            $table->dateTime('collected_at')->nullable();  // Sample le liya
            $table->dateTime('dispatched_at')->nullable(); // Lab ki taraf rowana
            $table->dateTime('received_at')->nullable();   // Lab me sample mila

            // GPS at collection point
            $table->decimal('collected_lat', 10, 7)->nullable();
            $table->decimal('collected_lng', 10, 7)->nullable();

            // Status
            $table->enum('status', [
                'Pending', 'Assigned', 'En Route', 'Arrived',
                'Collected', 'Dispatched', 'Received', 'Cancelled',
            ])->default('Pending');

            $table->text('notes')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['phlebotomist_id', 'scheduled_date']);
            $table->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_collections');
    }
};
