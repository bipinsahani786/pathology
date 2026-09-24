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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('api_key', 64)->nullable()->unique()->after('status');
            $table->string('api_allowed_origin')->nullable()->after('api_key');
            $table->boolean('api_enabled')->default(false)->after('api_allowed_origin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['api_key', 'api_allowed_origin', 'api_enabled']);
        });
    }
};
