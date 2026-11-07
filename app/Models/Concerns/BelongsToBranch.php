<?php

namespace App\Models\Concerns;

use App\Modules\multi_branch\Models\Entity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBranch
{
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }
}
