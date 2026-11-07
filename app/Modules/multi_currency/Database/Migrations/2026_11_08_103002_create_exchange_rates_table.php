<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_code', 3);
            $table->string('quote_code', 3);
            $table->decimal('rate', 18, 8);
            $table->date('as_of');
            $table->string('source')->nullable();
            $table->timestamps();

            $table->foreign('base_code')->references('code')->on('currencies')->cascadeOnDelete();
            $table->foreign('quote_code')->references('code')->on('currencies')->cascadeOnDelete();
            $table->unique(['base_code', 'quote_code', 'as_of']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
