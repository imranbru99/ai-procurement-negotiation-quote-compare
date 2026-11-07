<?php

namespace App\Modules\catalog\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class CatalogCategoryPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'catalog';
    }
}
