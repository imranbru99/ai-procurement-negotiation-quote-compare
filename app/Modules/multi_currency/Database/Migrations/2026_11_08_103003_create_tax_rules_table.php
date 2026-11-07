<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('country_code', 2);
            $table->string('name');
            $table->decimal('rate_percent', 8, 4);
            $table->string('type')->default('vat');
            $table->timestamps();

            $table->index(['tenant_id', 'country_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rules');
    }
};
