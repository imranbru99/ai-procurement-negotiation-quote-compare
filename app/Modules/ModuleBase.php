<?php

namespace App\Modules;

abstract class ModuleBase
{
    abstract public function key(): string;

    abstract public function name(): string;

    public function description(): ?string
    {
        return null;
    }

    public function icon(): ?string
    {
        return null;
    }

    public function category(): string
    {
        return 'procurement';
    }

    /**
     * @return list<string>
     */
    public function dependsOn(): array
    {
        return [];
    }

    public function isCore(): bool
    {
        return false;
    }

    public function defaultEnabled(): bool
    {
        return false;
    }

    /**
     * @return list<string>
     */
    public function permissions(): array
    {
        return [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function navigation(): array
    {
        return [];
    }

    public function migrationsPath(): ?string
    {
        $path = app_path('Modules/'.$this->key().'/Database/Migrations');

        return is_dir($path) ? $path : null;
    }
}
