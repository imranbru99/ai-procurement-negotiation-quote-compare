<?php

namespace App\Filament\Concerns;

use App\Facades\Module;

trait RegistersModuleNavigation
{
    abstract protected static function moduleKey(): string;

    public static function shouldRegisterNavigation(): bool
    {
        return Module::enabled(static::moduleKey());
    }
}
