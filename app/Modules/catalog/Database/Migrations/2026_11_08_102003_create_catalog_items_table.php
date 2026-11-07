<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('catalog_categories')->nullOnDelete();
            $table->string('sku');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('catalog_units')->nullOnDelete();
            $table->json('specs')->nullable();
            $table->decimal('base_price', 18, 4)->default(0);
            $table->string('currency_code', 3)->default('USD');
            $table->boolean('is_service')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('translations')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'sku']);
            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
