<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_integrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('branch_id')->nullable();

            // Machine Identity
            $table->string('name');                          // "Cellomax 5"
            $table->string('brand')->nullable();             // "INDO-MEDX"
            $table->enum('machine_type', [
                'biochemistry',
                'hematology',
                'electrolyte',
                'other',
            ]);

            // Connection Details
            $table->enum('connection_type', [
                'serial',      // RS-232 / USB-to-Serial
                'tcp',         // TCP/IP socket
                'http_push',   // Machine pushes HTTP directly
            ])->default('serial');

            $table->string('port_or_ip')->nullable();        // "COM3" or "192.168.1.50"
            $table->integer('baud_rate')->default(9600);     // For serial
            $table->integer('tcp_port')->nullable();         // For TCP

            // Protocol
            $table->enum('protocol', [
                'astm',        // ASTM E1394 (most biochemistry machines)
                'hl7',         // HL7 2.x
                'custom',      // Machine-specific proprietary
            ])->default('astm');

            // Parameter Mapping (JSON)
            // Maps machine param name -> lab test parameter name in our system
            // e.g. {"WBC": "WBC", "Glucose": "GLU", "Na": "Sodium"}
            $table->json('test_mapping')->nullable();

            // Security
            $table->string('api_token', 64)->unique();       // Bearer token for Bridge Agent

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable();   // Heartbeat from Bridge Agent

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_integrations');
    }
};
