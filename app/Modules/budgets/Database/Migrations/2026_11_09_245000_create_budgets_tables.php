<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('department')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('cost_center_id')->constrained('cost_centers')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->decimal('amount', 14, 2);
            $table->string('currency_code', 3)->default('USD');
            $table->decimal('committed', 14, 2)->default(0);
            $table->decimal('actual', 14, 2)->default(0);
            $table->timestamps();

            $table->unique(['cost_center_id', 'year']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreign('cost_center_id')->references('id')->on('cost_centers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['cost_center_id']);
        });

        Schema::dropIfExists('budgets');
        Schema::dropIfExists('cost_centers');
    }
};
