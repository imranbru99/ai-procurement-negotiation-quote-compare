<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class ThreeWayMatchPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'invoices';
    }
}
