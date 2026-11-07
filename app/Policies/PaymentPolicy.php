<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class PaymentPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'payments';
    }
}
