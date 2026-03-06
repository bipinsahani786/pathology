<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 20)->nullable()->index(); // Patient search by phone fast hoga
            $table->string('gender', 10)->nullable();
            $table->date('dob')->nullable();
            $table->text('address')->nullable();
            $table->string('avatar')->nullable();
            // Kisi specific patient ki extra medical history store karni ho toh
            $table->jsonb('meta_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
