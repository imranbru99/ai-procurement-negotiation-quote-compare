<?php

namespace App\Modules\documents\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\User;
use App\Modules\documents\Database\Factories\DocumentFactory;
use App\Modules\documents\Enums\DocumentVisibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    /** @use HasFactory<DocumentFactory> */
    use BelongsToTenant;

    use HasFactory;
    use InteractsWithMedia;
    use LogsActivity;

    protected static function booted(): void
    {
        static::creating(function (Document $document): void {
            if ($document->tenant_id === null && $document->documentable) {
                $document->tenant_id = $document->documentable->getAttribute('tenant_id');
            }
        });
    }

    protected $fillable = [
        'tenant_id',
        'documentable_type',
        'documentable_id',
        'title',
        'type',
        'version',
        'expires_at',
        'visibility',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'visibility' => DocumentVisibility::class,
        ];
    }

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')->singleFile();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['title', 'type', 'version', 'visibility'])->logOnlyDirty();
    }

    protected static function newFactory(): DocumentFactory
    {
        return DocumentFactory::new();
    }
}
