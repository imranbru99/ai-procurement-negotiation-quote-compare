<?php

namespace App\Policies;

use App\Policies\Concerns\ChecksModulePermissions;

class GoodsReceiptPolicy
{
    use ChecksModulePermissions;

    protected function moduleKey(): string
    {
        return 'goods_receiving';
    }
}
