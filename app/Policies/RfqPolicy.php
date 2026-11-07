<?php

namespace App\Policies;

use App\Models\Rfq;
use App\Models\User;

class RfqPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('rfq.view');
    }

    public function view(User $user, Rfq $rfq): bool
    {
        return $user->can('rfq.view') && $user->tenant_id === $rfq->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->can('rfq.create');
    }

    public function update(User $user, Rfq $rfq): bool
    {
        return $user->can('rfq.update') && $user->tenant_id === $rfq->tenant_id;
    }

    public function delete(User $user, Rfq $rfq): bool
    {
        return $user->can('rfq.delete') && $user->tenant_id === $rfq->tenant_id;
    }
}
