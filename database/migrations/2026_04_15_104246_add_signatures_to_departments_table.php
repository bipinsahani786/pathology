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
        Schema::table('departments', function (Blueprint $table) {
            $table->string('sig_1_path')->nullable();
            $table->string('sig_1_name')->nullable();
            $table->string('sig_1_desig')->nullable();
            
            $table->string('sig_2_path')->nullable();
            $table->string('sig_2_name')->nullable();
            $table->string('sig_2_desig')->nullable();
            
            $table->string('sig_3_path')->nullable();
            $table->string('sig_3_name')->nullable();
            $table->string('sig_3_desig')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn([
                'sig_1_path', 'sig_1_name', 'sig_1_desig',
                'sig_2_path', 'sig_2_name', 'sig_2_desig',
                'sig_3_path', 'sig_3_name', 'sig_3_desig'
            ]);
        });
    }
};
