<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class AwardPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'awards';
    }
}
