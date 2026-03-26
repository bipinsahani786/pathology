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
        Schema::table('settlements', function (Blueprint $table) {
            $table->string('status')->default('Pending')->after('type'); // Pending, Approved, Rejected
            $table->foreignId('collection_center_id')->nullable()->after('user_id')->constrained('collection_centers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settlements', function (Blueprint $table) {
            $table->dropForeign(['collection_center_id']);
            $table->dropColumn(['status', 'collection_center_id']);
        });
    }
};
