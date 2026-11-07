<?php

namespace App\Modules\attendance\Database\Seeders;

use App\Models\Tenant;
use App\Services\Tenancy\TenantContext;
use Illuminate\Database\Seeder;
use ImranDevBd\AttendanceHub\Models\AttendanceDevice;

class AttendanceModuleSeeder extends Seeder
{
    public function run(?Tenant $tenant = null): void
    {
        $tenant ??= Tenant::query()->first();
        if (! $tenant) {
            return;
        }

        TenantContext::set($tenant);

        AttendanceDevice::query()->firstOrCreate(
            [
                'tenant_id' => (string) $tenant->id,
                'serial_number' => 'VIRTUAL-DEMO-001',
            ],
            [
                'name' => 'Virtual Demo Device',
                'provider' => 'virtual',
                'model' => 'virtual',
                'ip' => '127.0.0.1',
                'port' => 4370,
                'status' => 'online',
            ]
        );

        TenantContext::clear();
    }
}
