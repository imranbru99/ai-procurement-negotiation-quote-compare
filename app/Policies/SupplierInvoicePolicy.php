<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class SupplierInvoicePolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'invoices';
    }
}
