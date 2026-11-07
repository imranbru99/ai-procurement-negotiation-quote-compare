<?php

use App\Models\Setting;
use App\Models\Tenant;
use App\Modules\suppliers\Enums\SupplierStatus;
use App\Modules\suppliers\Enums\SupplierTier;
use App\Modules\suppliers\Models\Supplier;
use App\Services\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('scopes tenant owned settings to the active tenant context', function (): void {
    $tenantA = Tenant::query()->create([
        'name' => 'Tenant A',
        'slug' => 'tenant-a',
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'USD',
        'is_active' => true,
    ]);

    $tenantB = Tenant::query()->create([
        'name' => 'Tenant B',
        'slug' => 'tenant-b',
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'USD',
        'is_active' => true,
    ]);

    Setting::query()->withoutGlobalScopes()->create([
        'tenant_id' => $tenantA->id,
        'group' => 'general',
        'key' => 'company_name',
        'value' => ['name' => 'Tenant A Co'],
    ]);

    Setting::query()->withoutGlobalScopes()->create([
        'tenant_id' => $tenantB->id,
        'group' => 'general',
        'key' => 'company_name',
        'value' => ['name' => 'Tenant B Co'],
    ]);

    TenantContext::set($tenantA);

    $visible = Setting::query()->pluck('tenant_id')->all();

    expect($visible)->toBe([$tenantA->id]);

    TenantContext::clear();
});

it('prevents tenant a from seeing tenant b suppliers via global scope', function (): void {
    $tenantA = Tenant::query()->create([
        'name' => 'Tenant A',
        'slug' => 'tenant-a-suppliers',
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'USD',
        'is_active' => true,
    ]);

    $tenantB = Tenant::query()->create([
        'name' => 'Tenant B',
        'slug' => 'tenant-b-suppliers',
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'USD',
        'is_active' => true,
    ]);

    $supplierA = Supplier::query()->withoutGlobalScopes()->create([
        'tenant_id' => $tenantA->id,
        'name' => 'Alpha Supplies',
        'slug' => 'alpha-supplies-'.Str::random(4),
        'email' => 'alpha@example.test',
        'currency_code' => 'USD',
        'tier' => SupplierTier::Standard->value,
        'status' => SupplierStatus::Approved->value,
    ]);

    $supplierB = Supplier::query()->withoutGlobalScopes()->create([
        'tenant_id' => $tenantB->id,
        'name' => 'Beta Supplies',
        'slug' => 'beta-supplies-'.Str::random(4),
        'email' => 'beta@example.test',
        'currency_code' => 'USD',
        'tier' => SupplierTier::Standard->value,
        'status' => SupplierStatus::Approved->value,
    ]);

    TenantContext::set($tenantA);

    $visibleIds = Supplier::query()->pluck('id')->all();

    expect($visibleIds)->toContain($supplierA->id)
        ->and($visibleIds)->not->toContain($supplierB->id)
        ->and(Supplier::query()->whereKey($supplierB->id)->exists())->toBeFalse();

    TenantContext::clear();
});
