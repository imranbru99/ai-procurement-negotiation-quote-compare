<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->string('type')->default('tenant')->after('tenant_id');
            $table->unsignedBigInteger('supplier_id')->nullable()->after('type');
            $table->string('locale', 16)->default('en')->after('password');
            $table->string('timezone', 64)->default('UTC')->after('locale');
            $table->boolean('is_active')->default(true)->after('timezone');
            $table->text('two_factor_secret')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('two_factor_secret');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn([
                'type',
                'supplier_id',
                'locale',
                'timezone',
                'is_active',
                'two_factor_secret',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};
