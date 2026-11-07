<?php

namespace App\Modules\catalog\Models;

use App\Services\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogUnit extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('catalog_unit_tenant', function (Builder $builder): void {
            $tenantId = TenantContext::getId();

            if ($tenantId !== null) {
                $builder->where(function (Builder $query) use ($tenantId): void {
                    $query->where($query->qualifyColumn('tenant_id'), $tenantId)
                        ->orWhereNull($query->qualifyColumn('tenant_id'));
                });
            }
        });

        static::creating(function (CatalogUnit $unit): void {
            if ($unit->getAttribute('tenant_id') === null && TenantContext::get()) {
                $unit->setAttribute('tenant_id', TenantContext::getId());
            }
        });
    }

    protected $table = 'catalog_units';

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CatalogItem::class, 'unit_id');
    }
}
