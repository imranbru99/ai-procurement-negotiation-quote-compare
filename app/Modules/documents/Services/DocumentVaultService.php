<?php

namespace App\Modules\documents\Services;

use App\Models\User;
use App\Modules\documents\Enums\DocumentVisibility;
use App\Modules\documents\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class DocumentVaultService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function store(
        Model $documentable,
        array $attributes,
        ?UploadedFile $file = null,
        ?User $uploader = null,
    ): Document {
        $document = new Document([
            'title' => $attributes['title'],
            'type' => $attributes['type'] ?? 'general',
            'version' => (int) ($attributes['version'] ?? 1),
            'expires_at' => $attributes['expires_at'] ?? null,
            'visibility' => $attributes['visibility'] ?? DocumentVisibility::Tenant,
            'uploaded_by' => $uploader?->id,
        ]);

        $documentable->documents()->save($document);

        if ($file !== null) {
            $document->addMedia($file)->toMediaCollection('file');
        }

        return $document->fresh(['media']);
    }

    public function bumpVersion(Document $document, ?UploadedFile $file = null): Document
    {
        $document->version = $document->version + 1;
        $document->save();

        if ($file !== null) {
            $document->clearMediaCollection('file');
            $document->addMedia($file)->toMediaCollection('file');
        }

        return $document->fresh(['media']);
    }
}
