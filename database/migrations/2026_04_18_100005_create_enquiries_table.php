<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('lab_name')->nullable();
            $table->string('lab_city')->nullable();
            $table->text('message')->nullable();
            $table->enum('enquiry_type', ['contact', 'enquiry', 'demo_request'])->default('contact');
            $table->enum('status', ['new', 'contacted', 'converted', 'closed'])->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
