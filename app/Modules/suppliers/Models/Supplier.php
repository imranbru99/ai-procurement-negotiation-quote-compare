<?php

namespace App\Modules\suppliers\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Modules\documents\Models\Document;
use App\Modules\suppliers\Database\Factories\SupplierFactory;
use App\Modules\suppliers\Enums\SupplierStatus;
use App\Modules\suppliers\Enums\SupplierTier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use ImranDev\UniversalSlug\Attributes\Slug;
use ImranDev\UniversalSlug\Traits\HasUniversalSlug;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Slug(from: 'name')]
class Supplier extends Model implements HasMedia
{
    /** @use HasFactory<SupplierFactory> */
    use BelongsToTenant;

    use HasFactory;
    use HasUniversalSlug;
    use InteractsWithMedia;
    use LogsActivity;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'name',
        'slug',
        'code',
        'legal_name',
        'email',
        'phone',
        'website',
        'country',
        'city',
        'address',
        'tax_id',
        'payment_terms',
        'currency_code',
        'tier',
        'status',
        'risk_score',
        'onboarding_notes',
        'bank_name',
        'bank_account',
        'bank_iban',
        'certifications',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'tier' => SupplierTier::class,
            'status' => SupplierStatus::class,
            'certifications' => 'array',
            'metadata' => 'array',
            'bank_account' => 'encrypted',
            'bank_iban' => 'encrypted',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SupplierCategory::class, 'category_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SupplierContact::class);
    }

    public function portalUsers(): HasMany
    {
        return $this->hasMany(SupplierUser::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name', 'code', 'status', 'tier', 'email', 'currency_code', 'category_id',
            ])
            ->logOnlyDirty();
    }

    protected static function newFactory(): SupplierFactory
    {
        return SupplierFactory::new();
    }
}
