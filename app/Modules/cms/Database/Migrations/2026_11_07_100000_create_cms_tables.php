<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('locale', 8)->index();
            $table->string('slug')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamps();

            $table->unique(['locale', 'slug']);
        });

        Schema::create('cms_pages', function (Blueprint $table): void {
            $table->id();
            $table->string('locale', 8)->index();
            $table->string('slug')->index();
            $table->string('title');
            $table->longText('body')->nullable();
            $table->string('body_format', 16)->default('html');
            $table->string('template')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['locale', 'slug']);
        });

        Schema::create('cms_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cms_category_id')->nullable()->constrained('cms_categories')->nullOnDelete();
            $table->string('locale', 8)->index();
            $table->string('slug')->index();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('body_format', 16)->default('html');
            $table->string('author_name')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['locale', 'slug']);
        });

        Schema::create('cms_faqs', function (Blueprint $table): void {
            $table->id();
            $table->string('locale', 8)->index();
            $table->string('category')->default('general')->index();
            $table->string('question');
            $table->text('answer');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_testimonials', function (Blueprint $table): void {
            $table->id();
            $table->string('locale', 8)->index();
            $table->string('name');
            $table->string('role')->nullable();
            $table->string('company')->nullable();
            $table->text('body');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_menus', function (Blueprint $table): void {
            $table->id();
            $table->string('locale', 8)->index();
            $table->string('location', 32)->index();
            $table->string('label');
            $table->string('url');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('cms_menus')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_menus');
        Schema::dropIfExists('cms_testimonials');
        Schema::dropIfExists('cms_faqs');
        Schema::dropIfExists('cms_posts');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('cms_categories');
    }
};
