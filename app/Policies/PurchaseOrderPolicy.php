<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class PurchaseOrderPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'purchase_orders';
    }
}
