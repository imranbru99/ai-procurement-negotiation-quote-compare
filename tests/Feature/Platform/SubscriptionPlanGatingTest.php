<?php

use App\Facades\Module;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('prevents enabling modules that are outside the active subscription plan', function (): void {
    $this->seed(DatabaseSeeder::class);

    $tenant = Tenant::query()->where('slug', 'urban-retail-co')->firstOrFail();
    $free = Plan::query()->where('slug', 'free')->firstOrFail();

    TenantSubscription::query()->updateOrCreate(
        ['tenant_id' => $tenant->id],
        ['plan_id' => $free->id, 'status' => 'active', 'starts_at' => now()],
    );

    expect(fn () => Module::enable('ai_negotiation', $tenant->id))
        ->toThrow(RuntimeException::class);

    expect(Module::enabled('ai_negotiation', $tenant->id))->toBeFalse();
});
