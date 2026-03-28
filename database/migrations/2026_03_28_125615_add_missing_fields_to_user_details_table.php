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
        Schema::table('user_details', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->after('company_id')->constrained()->cascadeOnDelete();
            $table->string('designation')->nullable()->after('branch_id');
            $table->string('profile_photo')->nullable()->after('designation');
        });

        // Optional: If avatar has data, move it to profile_photo
        // DB::statement('UPDATE user_details SET profile_photo = avatar WHERE avatar IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['company_id', 'branch_id', 'designation', 'profile_photo']);
        });
    }
};
