<?php

namespace App\Filament\App\Resources\PurchaseRequisitions\Pages;

use App\Filament\App\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use App\Models\PurchaseRequisition;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePurchaseRequisition extends CreateRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['requester_id'] = Auth::id();
        $data['status'] = PurchaseRequisition::STATUS_DRAFT;

        return $data;
    }
}
