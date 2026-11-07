<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class BudgetPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'budgets';
    }
}
