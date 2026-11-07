<?php

namespace App\Modules\suppliers\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class SupplierCategoryPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'suppliers';
    }
}
