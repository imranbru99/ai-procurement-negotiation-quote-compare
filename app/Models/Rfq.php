<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasComments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rfq extends Model
{
    use BelongsToTenant;
    use HasComments;
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'number',
        'title',
        'description',
        'status',
        'type',
        'deadline_at',
        'published_at',
        'requisition_id',
        'currency_code',
        'terms',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'deadline_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function requisition(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequisition::class, 'requisition_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(RfqLine::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(RfqInvitation::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(RfqQuestion::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function awards(): HasMany
    {
        return $this->hasMany(Award::class);
    }
}
