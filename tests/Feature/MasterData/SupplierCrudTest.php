<?php

use App\Facades\Module;
use App\Models\Tenant;
use App\Modules\suppliers\Models\Supplier;
use App\Modules\suppliers\Models\SupplierCategory;
use App\Services\Tenancy\TenantContext;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function (): void {
    $this->seed(RolesAndPermissionsSeeder::class);
    Module::syncFromManifests();

    $this->tenant = Tenant::query()->create([
        'name' => 'Test Tenant',
        'slug' => 'test-tenant-'.uniqid(),
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'USD',
        'is_active' => true,
    ]);

    Module::enable('suppliers', $this->tenant->id);
    TenantContext::set($this->tenant);
});

afterEach(fn () => TenantContext::clear());

it('creates updates and deletes suppliers within tenant context', function (): void {
    $category = SupplierCategory::factory()->create(['tenant_id' => $this->tenant->id]);

    $supplier = Supplier::query()->create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $category->id,
        'name' => 'Acme Parts Ltd',
        'code' => 'ACM-100',
        'currency_code' => 'USD',
    ]);

    expect($supplier->slug)->not->toBeEmpty();

    $supplier->update(['name' => 'Acme Parts International']);

    expect($supplier->fresh()->name)->toBe('Acme Parts International');

    $supplier->delete();

    expect(Supplier::query()->find($supplier->id))->toBeNull();
});

it('isolates suppliers by tenant', function (): void {
    $other = Tenant::query()->create([
        'name' => 'Other',
        'slug' => 'other-tenant',
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'USD',
        'is_active' => true,
    ]);

    Supplier::query()->withoutGlobalScopes()->create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Visible Supplier',
        'code' => 'VIS-1',
        'currency_code' => 'USD',
    ]);

    Supplier::query()->withoutGlobalScopes()->create([
        'tenant_id' => $other->id,
        'name' => 'Hidden Supplier',
        'code' => 'HID-1',
        'currency_code' => 'USD',
    ]);

    expect(Supplier::query()->pluck('name')->all())->toBe(['Visible Supplier']);
});
