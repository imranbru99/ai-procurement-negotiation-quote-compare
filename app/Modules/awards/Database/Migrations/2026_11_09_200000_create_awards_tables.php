<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('rfq_id')->constrained('rfqs')->cascadeOnDelete();
            $table->string('status')->default('draft');
            $table->foreignId('awarded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('justification')->nullable();
            $table->timestamp('awarded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('award_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('award_id')->constrained('awards')->cascadeOnDelete();
            $table->foreignId('rfq_line_id')->constrained('rfq_lines')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();
            $table->foreignId('quote_line_id')->constrained('quote_lines')->cascadeOnDelete();
            $table->decimal('qty', 14, 4);
            $table->decimal('unit_price', 14, 4);
            $table->string('currency_code', 3)->default('USD');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('award_lines');
        Schema::dropIfExists('awards');
    }
};
