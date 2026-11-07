<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'tenants', 'users', 'modules', 'settings', 'suppliers', 'catalog', 'requisitions',
            'approvals', 'rfq', 'quotes', 'quote_comparison', 'ai_negotiation', 'reverse_auctions',
            'awards', 'purchase_orders', 'contracts', 'goods_receiving', 'inventory', 'invoices',
            'payments', 'budgets', 'supplier_performance', 'supplier_risk', 'spend_analytics',
            'savings', 'price_intelligence', 'documents', 'tasks', 'collaboration', 'integrations',
            'subscription', 'support', 'cms', 'knowledge_base', 'multi_branch', 'multi_language',
            'backup_gdpr', 'audit_log', 'notifications', 'dashboard', 'attendance', 'multi_currency',
        ];

        $actions = ['view', 'create', 'update', 'delete', 'manage'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$module}.{$action}");
            }
        }

        $roles = [
            'super_admin' => Permission::all()->pluck('name')->all(),
            'tenant_owner' => $this->tenantOwnerPermissions($modules),
            'procurement_manager' => $this->managerPermissions($modules),
            'buyer' => $this->buyerPermissions(),
            'requester' => $this->requesterPermissions(),
            'approver' => $this->approverPermissions(),
            'finance' => $this->financePermissions(),
            'warehouse' => $this->warehousePermissions(),
            'contract_manager' => $this->contractPermissions(),
            'auditor' => $this->auditorPermissions($modules),
            'supplier' => $this->supplierPermissions(),
            'support_agent' => $this->supportPermissions(),
            'viewer' => $this->viewerPermissions($modules),
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName);
            $role->syncPermissions($permissions);
        }
    }

    /**
     * @param  list<string>  $modules
     * @return list<string>
     */
    protected function tenantOwnerPermissions(array $modules): array
    {
        return collect($modules)
            ->flatMap(fn (string $module): array => ["{$module}.view", "{$module}.create", "{$module}.update", "{$module}.delete", "{$module}.manage"])
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  list<string>  $modules
     * @return list<string>
     */
    protected function managerPermissions(array $modules): array
    {
        $focus = [
            'suppliers', 'catalog', 'requisitions', 'approvals', 'rfq', 'quotes', 'quote_comparison',
            'ai_negotiation', 'reverse_auctions', 'awards', 'purchase_orders', 'contracts',
            'supplier_performance', 'supplier_risk', 'spend_analytics', 'savings', 'price_intelligence',
        ];

        return collect($focus)
            ->flatMap(fn (string $module): array => ["{$module}.view", "{$module}.create", "{$module}.update", "{$module}.manage"])
            ->merge(['dashboard.view', 'notifications.view', 'documents.view', 'tasks.view', 'tasks.create'])
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    protected function buyerPermissions(): array
    {
        return [
            'rfq.view', 'rfq.create', 'rfq.update',
            'quotes.view', 'quotes.create', 'quotes.update',
            'quote_comparison.view',
            'suppliers.view', 'catalog.view',
            'requisitions.view', 'requisitions.create',
            'dashboard.view', 'notifications.view',
        ];
    }

    /**
     * @return list<string>
     */
    protected function requesterPermissions(): array
    {
        return [
            'requisitions.view', 'requisitions.create', 'requisitions.update',
            'catalog.view', 'dashboard.view', 'notifications.view',
        ];
    }

    /**
     * @return list<string>
     */
    protected function approverPermissions(): array
    {
        return [
            'approvals.view', 'approvals.update', 'approvals.manage',
            'requisitions.view', 'rfq.view', 'quotes.view',
            'dashboard.view', 'notifications.view',
        ];
    }

    /**
     * @return list<string>
     */
    protected function financePermissions(): array
    {
        return [
            'invoices.view', 'invoices.create', 'invoices.update', 'invoices.manage',
            'payments.view', 'payments.create', 'payments.update', 'payments.manage',
            'budgets.view', 'budgets.update',
            'purchase_orders.view', 'dashboard.view',
        ];
    }

    /**
     * @return list<string>
     */
    protected function warehousePermissions(): array
    {
        return [
            'goods_receiving.view', 'goods_receiving.create', 'goods_receiving.update',
            'inventory.view', 'inventory.update', 'inventory.manage',
            'purchase_orders.view', 'catalog.view', 'dashboard.view',
        ];
    }

    /**
     * @return list<string>
     */
    protected function contractPermissions(): array
    {
        return [
            'contracts.view', 'contracts.create', 'contracts.update', 'contracts.manage',
            'suppliers.view', 'awards.view', 'documents.view', 'dashboard.view',
        ];
    }

    /**
     * @param  list<string>  $modules
     * @return list<string>
     */
    protected function auditorPermissions(array $modules): array
    {
        return collect($modules)
            ->map(fn (string $module): string => "{$module}.view")
            ->merge(['audit_log.view', 'audit_log.manage', 'dashboard.view'])
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    protected function supplierPermissions(): array
    {
        return [
            'rfq.view', 'quotes.view', 'quotes.create', 'quotes.update',
            'reverse_auctions.view', 'purchase_orders.view', 'invoices.view',
            'documents.view', 'notifications.view', 'dashboard.view',
        ];
    }

    /**
     * @return list<string>
     */
    protected function supportPermissions(): array
    {
        return [
            'support.view', 'support.create', 'support.update', 'support.manage',
            'knowledge_base.view', 'cms.view', 'users.view', 'notifications.view',
            'dashboard.view',
        ];
    }

    /**
     * @param  list<string>  $modules
     * @return list<string>
     */
    protected function viewerPermissions(array $modules): array
    {
        return collect($modules)
            ->map(fn (string $module): string => "{$module}.view")
            ->merge(['dashboard.view'])
            ->unique()
            ->values()
            ->all();
    }
}
