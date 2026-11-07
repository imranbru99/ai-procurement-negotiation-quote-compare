<?php

use App\Models\Tenant;
use App\Services\Tenancy\TenantContext;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use ImranDevBd\AttendanceHub\Facades\AttendanceHub;
use ImranDevBd\AttendanceHub\Models\AttendanceDevice;

uses(RefreshDatabase::class);

it('syncs attendance using fake driver for virtual device', function (): void {
    $this->seed(DatabaseSeeder::class);

    AttendanceHub::fake();

    $tenant = Tenant::query()->firstOrFail();
    TenantContext::set($tenant);

    $device = AttendanceDevice::query()->where('provider', 'virtual')->firstOrFail();

    $logs = AttendanceHub::sync($device);

    expect(AttendanceHub::isFake())->toBeTrue()
        ->and($logs)->toBeInstanceOf(Collection::class);

    TenantContext::clear();
});
