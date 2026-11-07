<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reverse_auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rfq_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('status', 32)->default('draft');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedSmallInteger('auto_extend_minutes')->default(5);
            $table->decimal('min_bid_decrement', 16, 4)->default(1);
            $table->char('currency_code', 3)->default('USD');
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('auction_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained('reverse_auctions')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 16, 4);
            $table->timestamp('placed_at');
            $table->boolean('is_winning')->default(false);
            $table->timestamps();

            $table->index(['auction_id', 'is_winning']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auction_bids');
        Schema::dropIfExists('reverse_auctions');
    }
};
