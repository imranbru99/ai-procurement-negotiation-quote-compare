<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class StockItemPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'inventory';
    }
}
