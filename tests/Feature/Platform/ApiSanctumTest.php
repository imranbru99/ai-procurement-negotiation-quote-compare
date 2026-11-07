<?php

use App\Facades\Module;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\TenantContext;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->tenant = Tenant::query()->where('slug', 'urban-retail-co')->firstOrFail();

    $this->user = User::query()->where('email', 'buyer@urban-retail-co.test')->firstOrFail();

    TenantContext::set($this->tenant);
});

afterEach(function (): void {
    TenantContext::clear();
});

it('requires sanctum authentication for versioned api', function (): void {
    $this->getJson('/api/v1/suppliers')->assertUnauthorized();
});

it('returns not found when integrations module is disabled', function (): void {
    Module::disable('integrations', $this->tenant->id);

    Sanctum::actingAs($this->user);

    $this->getJson('/api/v1/suppliers')->assertNotFound();
});

it('returns not found when resource module is disabled', function (): void {
    Module::enable('integrations', $this->tenant->id);
    Module::disable('purchase_orders', $this->tenant->id);

    Sanctum::actingAs($this->user);

    $this->getJson('/api/v1/purchase-orders')->assertNotFound();
});

it('lists suppliers when integrations and suppliers are enabled', function (): void {
    Module::enable('integrations', $this->tenant->id);
    Module::enable('suppliers', $this->tenant->id);

    Sanctum::actingAs($this->user);

    $this->getJson('/api/v1/suppliers')
        ->assertOk()
        ->assertJsonStructure(['data']);
});
