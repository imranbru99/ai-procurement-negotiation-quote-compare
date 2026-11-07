<?php

namespace App\Modules\integrations\Http\Resources;

use App\Modules\suppliers\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Supplier
 */
class SupplierApiResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'status' => $this->status?->value ?? $this->status,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'tier' => $this->tier?->value ?? $this->tier,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
