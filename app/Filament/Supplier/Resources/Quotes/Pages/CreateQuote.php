<?php

namespace App\Filament\Supplier\Resources\Quotes\Pages;

use App\Filament\Supplier\Resources\Quotes\QuoteResource;
use App\Models\Rfq;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $rfq = Rfq::query()->findOrFail($data['rfq_id']);
        $data['supplier_id'] = $user->supplier_id;
        $data['tenant_id'] = $rfq->tenant_id;
        $data['status'] = 'draft';
        $data['source'] = 'portal';
        $data['number'] = 'Q-SUP-'.now()->format('YmdHis');

        return $data;
    }
}
