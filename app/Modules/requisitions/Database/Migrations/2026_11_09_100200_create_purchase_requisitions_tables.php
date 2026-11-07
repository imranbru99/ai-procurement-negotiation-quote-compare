<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('number');
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->string('department')->nullable();
            $table->string('cost_center')->nullable();
            $table->string('status')->default('draft');
            $table->string('urgency')->default('normal');
            $table->date('needed_by')->nullable();
            $table->decimal('budget_amount', 15, 2)->nullable();
            $table->string('currency_code', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'number']);
        });

        Schema::create('purchase_requisition_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('purchase_requisitions')->cascadeOnDelete();
            $table->foreignId('catalog_item_id')->nullable()->constrained('catalog_items')->nullOnDelete();
            $table->string('description');
            $table->decimal('qty', 15, 4)->default(1);
            $table->foreignId('unit_id')->nullable()->constrained('catalog_units')->nullOnDelete();
            $table->decimal('estimated_unit_price', 15, 4)->nullable();
            $table->string('currency_code', 3)->default('USD');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_lines');
        Schema::dropIfExists('purchase_requisitions');
    }
};
