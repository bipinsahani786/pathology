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
        Schema::table('invoices', function (Blueprint $table) {
            // Link to phlebotomist — after collection_type column
            $table->foreignId('phlebotomist_id')
                ->nullable()
                ->after('collection_type')
                ->constrained('users')
                ->nullOnDelete();

            // Home collection details (copied from POS for reference/printing)
            $table->text('home_collection_address')->nullable()->after('phlebotomist_id');
            $table->decimal('home_collection_lat', 10, 7)->nullable()->after('home_collection_address');
            $table->decimal('home_collection_lng', 10, 7)->nullable()->after('home_collection_lat');
            $table->date('home_scheduled_date')->nullable()->after('home_collection_lng');
            $table->time('home_scheduled_slot_start')->nullable()->after('home_scheduled_date');
            $table->time('home_scheduled_slot_end')->nullable()->after('home_scheduled_slot_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['phlebotomist_id']);
            $table->dropColumn([
                'phlebotomist_id',
                'home_collection_address',
                'home_collection_lat',
                'home_collection_lng',
                'home_scheduled_date',
                'home_scheduled_slot_start',
                'home_scheduled_slot_end',
            ]);
        });
    }
};
