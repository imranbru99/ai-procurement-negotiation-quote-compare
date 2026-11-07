<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('market_price_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_item_id')->nullable()->constrained('catalog_items')->nullOnDelete();
            $table->string('sku')->nullable();
            $table->string('source_url', 2048)->nullable();
            $table->string('source_name')->nullable();
            $table->decimal('price', 16, 4);
            $table->char('currency_code', 3)->default('USD');
            $table->timestamp('scraped_at');
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'catalog_item_id']);
            $table->index(['tenant_id', 'scraped_at']);
        });

        Schema::create('price_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_item_id')->constrained('catalog_items')->cascadeOnDelete();
            $table->decimal('threshold_percent', 8, 2);
            $table->string('direction', 16); // above|below
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'catalog_item_id', 'direction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_alerts');
        Schema::dropIfExists('market_price_points');
    }
};
