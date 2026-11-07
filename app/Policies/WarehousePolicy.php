<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class WarehousePolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'inventory';
    }
}
