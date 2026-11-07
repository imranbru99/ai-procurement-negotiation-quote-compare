<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->text('system_prompt')->nullable();
            $table->text('user_prompt_template');
            $table->json('output_schema')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'key']);
        });

        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('feature');
            $table->string('provider')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('prompt_tokens')->default(0);
            $table->unsignedInteger('completion_tokens')->default(0);
            $table->decimal('cost', 12, 6)->default(0);
            $table->string('status')->default('completed');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('feature');
            $table->string('status')->default('pending');
            $table->json('input');
            $table->json('output')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('quote_extractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_type')->default('paste');
            $table->longText('raw_text');
            $table->json('extracted')->nullable();
            $table->string('status')->default('pending_review');
            $table->timestamps();
        });

        Schema::create('negotiation_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rfq_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('open');
            $table->timestamps();
        });

        Schema::create('negotiation_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('negotiation_threads')->cascadeOnDelete();
            $table->unsignedSmallInteger('round_number');
            $table->string('direction');
            $table->json('offer_payload')->nullable();
            $table->text('message_body')->nullable();
            $table->string('channel')->default('note');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('ai_generated')->default(false);
            $table->decimal('delta_amount', 16, 4)->nullable();
            $table->decimal('savings_amount', 16, 4)->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('negotiation_strategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('negotiation_threads')->cascadeOnDelete();
            $table->decimal('target_price', 16, 4)->nullable();
            $table->decimal('walk_away_price', 16, 4)->nullable();
            $table->decimal('batna', 16, 4)->nullable();
            $table->json('leverage_points')->nullable();
            $table->json('concession_plan')->nullable();
            $table->text('reasoning')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negotiation_strategies');
        Schema::dropIfExists('negotiation_rounds');
        Schema::dropIfExists('negotiation_threads');
        Schema::dropIfExists('quote_extractions');
        Schema::dropIfExists('ai_jobs');
        Schema::dropIfExists('ai_usage_logs');
        Schema::dropIfExists('ai_prompt_templates');
    }
};
