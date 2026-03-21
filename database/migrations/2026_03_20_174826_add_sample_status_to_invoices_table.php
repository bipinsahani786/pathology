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
            $table->string('sample_status')->default('Pending')->after('status'); // Pending, Collected, Dispatched, Received, Processing, Ready
            $table->dateTime('sample_collected_at')->nullable()->after('sample_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['sample_status', 'sample_collected_at']);
        });
    }
};
