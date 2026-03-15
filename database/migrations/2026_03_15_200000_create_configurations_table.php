<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('config_key');
            $table->text('config_value')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'config_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
