<?php

declare(strict_types=1);

$base = dirname(__DIR__).'/app/Modules';

$core = [
    'tenants' => ['Tenants', 'core', true, 10, []],
    'users' => ['Users', 'core', true, 20, ['tenants']],
    'modules' => ['Modules', 'core', true, 30, ['tenants']],
    'settings' => ['Settings', 'core', true, 40, ['tenants']],
    'audit_log' => ['Audit Log', 'core', true, 50, ['tenants']],
    'notifications' => ['Notifications', 'core', true, 60, ['users']],
    'dashboard' => ['Dashboard', 'core', true, 70, ['users']],
];

$procurement = [
    'suppliers' => ['Suppliers', 'procurement', false, 100, ['tenants', 'users']],
    'catalog' => ['Catalog', 'procurement', false, 110, ['suppliers']],
    'requisitions' => ['Requisitions', 'procurement', false, 120, ['catalog', 'users']],
    'approvals' => ['Approvals', 'procurement', false, 130, ['requisitions']],
    'rfq' => ['RFQ', 'procurement', false, 140, ['requisitions', 'suppliers', 'catalog']],
    'quotes' => ['Quotes', 'procurement', false, 150, ['rfq', 'suppliers', 'catalog']],
    'quote_comparison' => ['Quote Comparison', 'procurement', false, 160, ['quotes']],
    'ai_negotiation' => ['AI Negotiation', 'procurement', false, 170, ['quote_comparison']],
    'reverse_auctions' => ['Reverse Auctions', 'procurement', false, 180, ['rfq', 'suppliers']],
    'awards' => ['Awards', 'procurement', false, 190, ['quote_comparison', 'reverse_auctions']],
    'purchase_orders' => ['Purchase Orders', 'procurement', false, 200, ['awards']],
    'contracts' => ['Contracts', 'procurement', false, 210, ['suppliers', 'awards']],
    'goods_receiving' => ['Goods Receiving', 'procurement', false, 220, ['purchase_orders']],
    'inventory' => ['Inventory', 'procurement', false, 230, ['goods_receiving', 'catalog']],
    'invoices' => ['Invoices', 'procurement', false, 240, ['goods_receiving']],
    'payments' => ['Payments', 'procurement', false, 250, ['invoices']],
    'budgets' => ['Budgets', 'procurement', false, 260, ['tenants']],
    'supplier_performance' => ['Supplier Performance', 'procurement', false, 270, ['suppliers', 'purchase_orders']],
    'supplier_risk' => ['Supplier Risk', 'procurement', false, 280, ['suppliers']],
    'spend_analytics' => ['Spend Analytics', 'procurement', false, 290, ['purchase_orders', 'payments']],
    'savings' => ['Savings', 'procurement', false, 300, ['spend_analytics', 'ai_negotiation']],
    'price_intelligence' => ['Price Intelligence', 'procurement', false, 310, ['catalog', 'quotes']],
    'attendance' => ['Attendance', 'procurement', false, 320, ['users']],
    'multi_currency' => ['Multi Currency', 'procurement', false, 330, ['settings']],
    'documents' => ['Documents', 'procurement', false, 340, ['tenants']],
    'tasks' => ['Tasks', 'procurement', false, 350, ['users', 'collaboration']],
    'collaboration' => ['Collaboration', 'procurement', false, 360, ['users', 'notifications']],
    'integrations' => ['Integrations', 'procurement', false, 370, ['settings']],
    'subscription' => ['Subscription', 'procurement', false, 380, ['tenants']],
    'support' => ['Support', 'procurement', false, 390, ['users']],
    'cms' => ['CMS', 'procurement', false, 400, ['documents']],
    'knowledge_base' => ['Knowledge Base', 'procurement', false, 410, ['cms']],
    'multi_branch' => ['Multi Branch', 'procurement', false, 420, ['tenants']],
    'multi_language' => ['Multi Language', 'procurement', false, 430, ['settings']],
    'backup_gdpr' => ['Backup & GDPR', 'procurement', false, 440, ['audit_log', 'settings']],
];

$all = $core + $procurement;

foreach ($all as $key => [$name, $category, $isCore, $sort, $dependsOn]) {
    $dir = $base.'/'.$key;
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $defaultEnabled = $isCore;
    $description = "ProcureAI module: {$name}.";

    $permissions = [
        "{$key}.view",
        "{$key}.create",
        "{$key}.update",
        "{$key}.delete",
    ];

    $manifest = [
        'key' => $key,
        'name' => $name,
        'description' => $description,
        'icon' => 'heroicon-o-cube',
        'category' => $category,
        'depends_on' => $dependsOn,
        'is_core' => $isCore,
        'default_enabled' => $defaultEnabled,
        'sort' => $sort,
        'permissions' => $permissions,
        'navigation' => [],
    ];

    file_put_contents(
        $dir.'/module.json',
        json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
    );

    $provider = <<<PHP
<?php

namespace App\\Modules\\{$key};

use App\\Facades\\Module;
use Illuminate\\Support\\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! Module::enabled('{$key}')) {
            return;
        }
    }

    public function boot(): void
    {
        if (! Module::enabled('{$key}')) {
            return;
        }
    }
}

PHP;

    file_put_contents($dir.'/ModuleServiceProvider.php', $provider);
}

echo 'Generated '.count($all)." modules.\n";
