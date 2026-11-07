<?php

namespace App\Modules\multi_currency\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class CurrencyPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'multi_currency';
    }
}
