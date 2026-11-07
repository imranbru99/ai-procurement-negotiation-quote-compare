<?php

namespace Database\Seeders;

use App\Facades\Module;
use App\Models\ApprovalWorkflow;
use App\Models\ModuleDefinition;
use App\Models\Plan;
use App\Models\PurchaseRequisition;
use App\Models\Tenant;
use App\Models\TenantSubscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DemoTenantsSeeder extends Seeder
{
    /** @var array<string, string> */
    public const ROLE_EMAIL_LOCAL_PARTS = [
        'tenant_owner' => 'owner',
        'procurement_manager' => 'procurement-manager',
        'buyer' => 'buyer',
        'requester' => 'requester',
        'approver' => 'approver',
        'finance' => 'finance',
        'warehouse' => 'warehouse',
        'contract_manager' => 'contract-manager',
        'auditor' => 'auditor',
        'support_agent' => 'support',
        'viewer' => 'viewer',
    ];

    public function run(): void
    {
        foreach ($this->tenantDefinitions() as $definition) {
            $this->seedTenant($definition);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function tenantDefinitions(): array
    {
        return [
            [
                'slug' => 'acme-manufacturing',
                'name' => 'Acme Manufacturing',
                'industry' => 'manufacturing',
                'plan' => 'enterprise',
                'module_mode' => 'all',
            ],
            [
                'slug' => 'urban-retail-co',
                'name' => 'Urban Retail Co',
                'industry' => 'retail',
                'plan' => 'starter',
                'modules' => [
                    'suppliers', 'catalog', 'requisitions', 'approvals', 'rfq', 'quotes',
                    'quote_comparison', 'ai_negotiation', 'savings', 'multi_currency', 'documents',
                ],
            ],
            [
                'slug' => 'buildright-construction',
                'name' => 'BuildRight Construction',
                'industry' => 'construction',
                'plan' => 'pro',
                'modules' => [
                    'suppliers', 'catalog', 'requisitions', 'approvals', 'rfq', 'quotes',
                    'quote_comparison', 'ai_negotiation', 'savings', 'awards', 'purchase_orders',
                    'goods_receiving', 'inventory', 'contracts', 'invoices', 'payments', 'budgets',
                    'attendance', 'multi_currency', 'documents', 'supplier_performance', 'supplier_risk',
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    protected function seedTenant(array $definition): void
    {
        $tenant = Tenant::query()->updateOrCreate(
            ['slug' => $definition['slug']],
            [
                'name' => $definition['name'],
                'industry' => $definition['industry'],
                'timezone' => 'UTC',
                'locale' => 'en',
                'currency' => 'USD',
                'is_active' => true,
            ],
        );

        $this->enableModules($tenant, $definition);

        if (Schema::hasTable('tenant_subscriptions')) {
            $plan = Plan::query()->where('slug', $definition['plan'])->first();
            if ($plan) {
                TenantSubscription::query()->updateOrCreate(
                    ['tenant_id' => $tenant->id],
                    [
                        'plan_id' => $plan->id,
                        'starts_at' => now()->subMonth(),
                        'status' => 'active',
                    ],
                );
            }
        }

        $users = $this->seedRoleUsers($tenant);
        $this->seedApprovalWorkflow($tenant);

        $this->callWith(TenantVolumeSeeder::class, [
            'tenant' => $tenant,
            'users' => $users,
            'definition' => $definition,
        ]);
    }

    /**
     * @param  array<string, mixed>  $definition
     */
    protected function enableModules(Tenant $tenant, array $definition): void
    {
        if (($definition['module_mode'] ?? null) === 'all') {
            ModuleDefinition::query()
                ->where('is_core', false)
                ->pluck('key')
                ->each(fn (string $key) => Module::enable($key, $tenant->id));

            return;
        }

        foreach ($definition['modules'] ?? [] as $module) {
            Module::enable($module, $tenant->id);
        }
    }

    /**
     * @return array<string, User>
     */
    protected function seedRoleUsers(Tenant $tenant): array
    {
        $users = [];

        foreach (self::ROLE_EMAIL_LOCAL_PARTS as $role => $local) {
            $email = "{$local}@{$tenant->slug}.test";
            $user = User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => ucfirst(str_replace('-', ' ', $local)).' ('.$tenant->name.')',
                    'password' => Hash::make('password'),
                    'type' => 'tenant',
                    'tenant_id' => $tenant->id,
                    'locale' => 'en',
                    'timezone' => $tenant->timezone,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );
            $user->syncRoles([$role]);
            $users[$role] = $user;
        }

        return $users;
    }

    protected function seedApprovalWorkflow(Tenant $tenant): void
    {
        ApprovalWorkflow::query()->firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'entity_type' => PurchaseRequisition::class,
            ],
            [
                'name' => 'Default requisition approval',
                'rules' => ['levels' => [['level' => 1]]],
                'is_active' => true,
            ],
        );
    }
}
