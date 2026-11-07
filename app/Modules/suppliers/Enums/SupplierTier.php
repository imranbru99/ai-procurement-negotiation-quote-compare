<?php

namespace App\Modules\suppliers\Enums;

enum SupplierTier: string
{
    case Preferred = 'preferred';
    case Standard = 'standard';
    case Probation = 'probation';
    case Blacklisted = 'blacklisted';
}
