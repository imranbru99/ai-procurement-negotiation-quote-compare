<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('award_id')->nullable()->constrained('awards')->nullOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->string('number');
            $table->string('status')->default('draft');
            $table->string('currency_code', 3)->default('USD');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->unsignedInteger('revision')->default(1);
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('cost_center_id')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'number']);
        });

        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->foreignId('award_line_id')->nullable()->constrained('award_lines')->nullOnDelete();
            $table->foreignId('rfq_line_id')->nullable()->constrained('rfq_lines')->nullOnDelete();
            $table->string('description');
            $table->decimal('qty', 14, 4);
            $table->decimal('qty_received', 14, 4)->default(0);
            $table->decimal('unit_price', 14, 4);
            $table->string('currency_code', 3)->default('USD');
            $table->unsignedInteger('line_number')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_lines');
        Schema::dropIfExists('purchase_orders');
    }
};
