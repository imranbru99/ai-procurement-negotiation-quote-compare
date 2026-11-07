<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('risk_score')->default(50);
            $table->boolean('financial_flag')->default(false);
            $table->boolean('geo_flag')->default(false);
            $table->boolean('compliance_flag')->default(false);
            $table->text('sanctions_notes')->nullable();
            $table->json('document_expiry_flags')->nullable();
            $table->text('ai_reasoning')->nullable();
            $table->timestamp('assessed_at');
            $table->timestamps();

            $table->index(['tenant_id', 'supplier_id', 'assessed_at'], 'supplier_risk_tenant_supplier_assessed_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_risk_assessments');
    }
};
