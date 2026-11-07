<?php

namespace App\Modules\documents\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class DocumentPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'documents';
    }
}
