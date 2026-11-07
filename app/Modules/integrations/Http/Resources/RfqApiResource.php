<?php

namespace App\Modules\integrations\Http\Resources;

use App\Models\Rfq;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Rfq */
class RfqApiResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'title' => $this->title,
            'status' => $this->status,
            'type' => $this->type,
            'deadline_at' => $this->deadline_at?->toIso8601String(),
            'currency_code' => $this->currency_code,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
