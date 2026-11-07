<?php

use App\Models\AiPromptTemplate;
use App\Models\Tenant;
use App\Services\Ai\AiService;
use App\Services\Tenancy\TenantContext;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(DatabaseSeeder::class);

    $this->tenant = Tenant::query()->create([
        'name' => 'Demo tenant',
        'slug' => 'demo-ai',
        'timezone' => 'UTC',
        'locale' => 'en',
        'currency' => 'USD',
        'is_active' => true,
    ]);

    TenantContext::set($this->tenant);
    config(['ai.demo_mode' => true]);
});

afterEach(fn () => TenantContext::clear());

it('returns deterministic quote analysis in demo mode', function (): void {
    expect(AiPromptTemplate::query()->where('key', 'quote_analysis')->exists())->toBeTrue();

    $service = app(AiService::class);
    $response = $service->runPromptTemplate('quote_analysis', [
        'comparison_json' => '{"quotes":[]}',
    ]);

    $payload = json_decode($response->content, true);

    expect($payload)->toBeArray()
        ->and($payload['summary'] ?? null)->toContain('Demo analysis');
});
