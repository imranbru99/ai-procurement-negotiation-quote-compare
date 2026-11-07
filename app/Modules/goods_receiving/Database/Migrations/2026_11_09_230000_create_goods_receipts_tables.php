<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('po_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->string('number');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('draft');
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('attendance_presence_note')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'number']);
        });

        Schema::create('goods_receipt_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_id')->constrained('goods_receipts')->cascadeOnDelete();
            $table->foreignId('po_line_id')->constrained('purchase_order_lines')->cascadeOnDelete();
            $table->decimal('qty_received', 14, 4);
            $table->decimal('qty_accepted', 14, 4)->default(0);
            $table->decimal('qty_rejected', 14, 4)->default(0);
            $table->string('qc_status')->default('pending');
            $table->text('discrepancy_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_lines');
        Schema::dropIfExists('goods_receipts');
    }
};
