<?php

namespace Database\Seeders;

use App\Models\ModuleDefinition;
use App\Models\Plan;
use App\Models\PlanModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('plans')) {
            return;
        }

        $allKeys = ModuleDefinition::query()->pluck('key')->all();

        $matrices = [
            'free' => [
                'suppliers', 'catalog', 'requisitions', 'documents', 'multi_currency',
                'dashboard', 'settings', 'modules', 'users', 'notifications',
            ],
            'starter' => [
                'suppliers', 'catalog', 'requisitions', 'approvals', 'rfq', 'quotes',
                'documents', 'multi_currency', 'savings', 'dashboard', 'settings',
                'modules', 'users', 'notifications', 'integrations', 'collaboration',
                'tasks', 'support', 'subscription', 'backup_gdpr', 'multi_branch',
            ],
            'pro' => [
                'suppliers', 'catalog', 'requisitions', 'approvals', 'rfq', 'quotes',
                'quote_comparison', 'ai_negotiation', 'awards', 'purchase_orders',
                'goods_receiving', 'invoices', 'payments', 'budgets', 'contracts',
                'savings', 'supplier_performance', 'supplier_risk', 'documents',
                'multi_currency', 'dashboard', 'settings', 'modules', 'users', 'notifications',
            ],
            'enterprise' => $allKeys,
        ];

        $plans = [
            'free' => ['name' => 'Free', 'price' => 0, 'sort' => 10, 'description' => 'Core catalog and requisitions.'],
            'starter' => ['name' => 'Starter', 'price' => 4900, 'sort' => 20, 'description' => 'RFQ and quote collection.'],
            'pro' => ['name' => 'Pro', 'price' => 14900, 'sort' => 30, 'description' => 'AI negotiation and procure-to-pay.'],
            'enterprise' => ['name' => 'Enterprise', 'price' => 49900, 'sort' => 40, 'description' => 'All modules and branches.'],
        ];

        foreach ($plans as $slug => $meta) {
            $plan = Plan::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $meta['name'],
                    'price_monthly_cents' => $meta['price'],
                    'description' => $meta['description'],
                    'sort_order' => $meta['sort'],
                    'is_active' => true,
                ],
            );

            $enabled = array_unique($matrices[$slug]);
            PlanModule::query()->where('plan_id', $plan->id)->delete();

            $rows = collect($enabled)->map(fn (string $key): array => [
                'plan_id' => $plan->id,
                'module_key' => $key,
                'enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            PlanModule::query()->insert($rows);
        }
    }
}
