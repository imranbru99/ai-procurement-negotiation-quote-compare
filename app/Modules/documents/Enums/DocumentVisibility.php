<?php

namespace App\Modules\documents\Enums;

enum DocumentVisibility: string
{
    case Private = 'private';
    case Tenant = 'tenant';
    case Public = 'public';
}
