<?php

use App\Models\Tenant;
use App\Modules\backup_gdpr\Services\GdprService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

it('creates a gdpr export zip file on disk', function (): void {
    $this->seed(DatabaseSeeder::class);

    $tenant = Tenant::query()->firstOrFail();

    $path = app(GdprService::class)->exportTenantData($tenant);

    expect(File::exists($path))->toBeTrue()
        ->and(str_ends_with($path, '.zip'))->toBeTrue();
});
