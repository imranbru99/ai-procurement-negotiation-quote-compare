<?php

namespace App\Policies;

use App\Models\PurchaseRequisition;
use App\Models\User;

class PurchaseRequisitionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('requisitions.view');
    }

    public function view(User $user, PurchaseRequisition $requisition): bool
    {
        return $user->can('requisitions.view')
            && $user->tenant_id === $requisition->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->can('requisitions.create');
    }

    public function update(User $user, PurchaseRequisition $requisition): bool
    {
        return $user->can('requisitions.update')
            && $user->tenant_id === $requisition->tenant_id
            && $requisition->status === PurchaseRequisition::STATUS_DRAFT;
    }

    public function delete(User $user, PurchaseRequisition $requisition): bool
    {
        return $user->can('requisitions.delete')
            && $user->tenant_id === $requisition->tenant_id
            && $requisition->status === PurchaseRequisition::STATUS_DRAFT;
    }

    public function submit(User $user, PurchaseRequisition $requisition): bool
    {
        return $user->can('requisitions.update')
            && $user->tenant_id === $requisition->tenant_id;
    }
}
