<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class ContractClausePolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'contracts';
    }
}
