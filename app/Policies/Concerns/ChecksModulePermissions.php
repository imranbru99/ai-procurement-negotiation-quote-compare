<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait ChecksModulePermissions
{
    abstract protected function moduleKey(): string;

    public function viewAny(User $user): bool
    {
        return $user->can("{$this->moduleKey()}.view");
    }

    public function view(User $user, mixed $model): bool
    {
        return $user->can("{$this->moduleKey()}.view");
    }

    public function create(User $user): bool
    {
        return $user->can("{$this->moduleKey()}.create");
    }

    public function update(User $user, mixed $model): bool
    {
        return $user->can("{$this->moduleKey()}.update");
    }

    public function delete(User $user, mixed $model): bool
    {
        return $user->can("{$this->moduleKey()}.delete");
    }
}
