<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class CostCenterPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'budgets';
    }
}
