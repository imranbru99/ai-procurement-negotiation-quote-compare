<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kb_articles', function (Blueprint $table): void {
            $table->id();
            $table->string('locale', 8)->index();
            $table->string('slug')->index();
            $table->string('title');
            $table->string('category')->nullable()->index();
            $table->longText('body')->nullable();
            $table->string('body_format', 16)->default('html');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['locale', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kb_articles');
    }
};
