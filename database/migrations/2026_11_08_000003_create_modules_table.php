<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('category')->default('procurement');
            $table->json('depends_on')->nullable();
            $table->boolean('is_core')->default(false);
            $table->boolean('default_enabled')->default(false);
            $table->unsignedInteger('sort')->default(100);
            $table->json('permissions')->nullable();
            $table->json('navigation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
