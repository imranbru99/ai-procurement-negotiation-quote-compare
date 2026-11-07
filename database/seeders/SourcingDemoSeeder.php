<?php

namespace Database\Seeders;

use App\Facades\Module;
use App\Models\ApprovalWorkflow;
use App\Models\PurchaseRequisition;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SourcingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->firstOrCreate(
            ['slug' => 'acme-sourcing'],
            [
                'name' => 'Acme Procurement',
                'timezone' => 'UTC',
                'locale' => 'en',
                'currency' => 'USD',
                'is_active' => true,
            ]
        );

        foreach (['catalog', 'suppliers', 'requisitions', 'approvals', 'rfq', 'quotes'] as $module) {
            Module::enable($module, $tenant->id);
        }

        ApprovalWorkflow::query()->firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'entity_type' => PurchaseRequisition::class,
            ],
            [
                'name' => 'Default requisition approval',
                'rules' => ['levels' => [['level' => 1]]],
                'is_active' => true,
            ]
        );

        $buyer = User::query()->updateOrCreate(
            ['email' => 'buyer@acme.test'],
            [
                'name' => 'Acme Buyer',
                'password' => Hash::make('password'),
                'type' => 'tenant',
                'tenant_id' => $tenant->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $buyer->assignRole('buyer');

        $approver = User::query()->updateOrCreate(
            ['email' => 'approver@acme.test'],
            [
                'name' => 'Acme Approver',
                'password' => Hash::make('password'),
                'type' => 'tenant',
                'tenant_id' => $tenant->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $approver->assignRole('approver');
    }
}
