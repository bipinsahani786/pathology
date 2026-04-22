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
        // 1. Suppliers
        Schema::create('inventory_suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('gst_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Categories
        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // 3. Items Master
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('inventory_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('unit')->default('pcs'); // ml, pkt, box, etc.
            $table->decimal('min_stock_level', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Stock per Branch
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('quantity', 15, 2)->default(0);
            $table->timestamps();
        });

        // 5. Batches for Expiry Tracking
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_stock_id')->constrained('inventory_stocks')->onDelete('cascade');
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('mrp', 15, 2)->default(0);
            $table->timestamps();
        });

        // 6. Transactions & Staff Issuance
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->enum('type', ['in', 'out'])->comment('in: stock added, out: stock removed');
            $table->decimal('quantity', 15, 2);
            $table->string('source')->comment('purchase, consumption, wastage, adjustment, transfer');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('performed_by_id')->constrained('users');
            $table->foreignId('issued_to_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 7. Transfers between branches
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('to_branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('quantity', 15, 2);
            $table->enum('status', ['pending', 'shipped', 'received', 'cancelled'])->default('pending');
            $table->foreignId('created_by_id')->constrained('users');
            $table->timestamps();
        });

        // 8. Equipment/Assets
        Schema::create('inventory_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('name');
            $table->string('model_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->date('last_service_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->date('last_calibration_date')->nullable();
            $table->date('next_calibration_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_equipment');
        Schema::dropIfExists('inventory_transfers');
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_batches');
        Schema::dropIfExists('inventory_stocks');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_categories');
        Schema::dropIfExists('inventory_suppliers');
    }
};
