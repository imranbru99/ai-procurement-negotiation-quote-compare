<?php

namespace App\Modules\suppliers\Policies;

use App\Models\User;
use App\Modules\suppliers\Models\Supplier;
use App\Policies\Concerns\ChecksModulePermissions;

class SupplierPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'suppliers';
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->can('suppliers.delete');
    }
}
