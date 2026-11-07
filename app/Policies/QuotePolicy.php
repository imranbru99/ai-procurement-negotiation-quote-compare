<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\User;

class QuotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('quotes.view');
    }

    public function view(User $user, Quote $quote): bool
    {
        if ($user->type === 'supplier') {
            return $user->supplier_id === $quote->supplier_id;
        }

        return $user->can('quotes.view') && $user->tenant_id === $quote->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->can('quotes.create');
    }

    public function update(User $user, Quote $quote): bool
    {
        if ($user->type === 'supplier') {
            return $user->supplier_id === $quote->supplier_id && in_array($quote->status, ['draft', 'revised'], true);
        }

        return $user->can('quotes.update') && $user->tenant_id === $quote->tenant_id;
    }

    public function delete(User $user, Quote $quote): bool
    {
        return $user->can('quotes.delete') && $user->tenant_id === $quote->tenant_id;
    }
}
