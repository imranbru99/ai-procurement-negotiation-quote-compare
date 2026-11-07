<?php

use App\Facades\Module;
use App\Models\ModuleDefinition;
use App\Models\Tenant;
use App\Services\Tenancy\TenantContext;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->tenant = Tenant::query()->firstOrCreate(
        ['slug' => 'acme-module-test'],
        [
            'name' => 'Acme Procurement',
            'timezone' => 'UTC',
            'locale' => 'en',
            'currency' => 'USD',
            'is_active' => true,
        ],
    );

    TenantContext::set($this->tenant);
});

afterEach(function (): void {
    TenantContext::clear();
});

it('reflects enable and disable for rfq, quotes, and quote comparison', function (): void {
    expect(Module::enabled('rfq', $this->tenant->id))->toBeFalse()
        ->and(Module::enabled('quotes', $this->tenant->id))->toBeFalse()
        ->and(Module::enabled('quote_comparison', $this->tenant->id))->toBeFalse();

    Module::enable('rfq', $this->tenant->id);

    expect(Module::enabled('rfq', $this->tenant->id))->toBeTrue()
        ->and(Module::enabled('quotes', $this->tenant->id))->toBeFalse();

    Module::enable('quotes', $this->tenant->id);

    expect(Module::enabled('quotes', $this->tenant->id))->toBeTrue();

    Module::enable('quote_comparison', $this->tenant->id);

    expect(Module::enabled('quote_comparison', $this->tenant->id))->toBeTrue();

    Module::enable('ai_negotiation', $this->tenant->id);

    expect(Module::enabled('ai_negotiation', $this->tenant->id))->toBeTrue();

    Module::disable('ai_negotiation', $this->tenant->id);
    Module::disable('quote_comparison', $this->tenant->id);
    Module::disable('quotes', $this->tenant->id);
    Module::disable('rfq', $this->tenant->id);

    expect(Module::enabled('quote_comparison', $this->tenant->id))->toBeFalse()
        ->and(Module::enabled('ai_negotiation', $this->tenant->id))->toBeFalse()
        ->and(Module::enabled('quotes', $this->tenant->id))->toBeFalse()
        ->and(Module::enabled('rfq', $this->tenant->id))->toBeFalse();
});

it('keeps core modules enabled regardless of tenant toggles', function (): void {
    expect(Module::enabled('dashboard', $this->tenant->id))->toBeTrue()
        ->and(Module::enabled('modules', $this->tenant->id))->toBeTrue();
});

it('can enable and disable every non-core module from definitions', function (): void {
    $keys = ModuleDefinition::query()
        ->where('is_core', false)
        ->orderBy('sort')
        ->pluck('key');

    expect($keys)->not->toBeEmpty();

    foreach ($keys as $key) {
        Module::enable($key, $this->tenant->id);
        expect(Module::enabled($key, $this->tenant->id))->toBeTrue();
    }

    $remaining = $keys->all();

    while ($remaining !== []) {
        $progress = false;

        foreach ($remaining as $index => $key) {
            $blockingDependents = collect(Module::dependents($key))
                ->filter(fn (string $dependent): bool => Module::enabled($dependent, $this->tenant->id));

            if ($blockingDependents->isNotEmpty()) {
                continue;
            }

            Module::disable($key, $this->tenant->id);
            expect(Module::enabled($key, $this->tenant->id))->toBeFalse();
            unset($remaining[$index]);
            $progress = true;
        }

        $remaining = array_values($remaining);

        expect($progress)->toBeTrue();
    }
});
