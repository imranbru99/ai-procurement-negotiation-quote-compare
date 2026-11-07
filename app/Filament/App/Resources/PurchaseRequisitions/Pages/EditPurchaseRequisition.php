<?php

namespace App\Filament\App\Resources\PurchaseRequisitions\Pages;

use App\Filament\App\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use App\Models\PurchaseRequisition;
use App\Modules\requisitions\Services\RequisitionService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseRequisition extends EditRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('submit')
                ->label('Submit for approval')
                ->visible(fn (PurchaseRequisition $record): bool => $record->status === PurchaseRequisition::STATUS_DRAFT)
                ->requiresConfirmation()
                ->action(function (PurchaseRequisition $record, RequisitionService $service): void {
                    $service->submit($record);
                    Notification::make()->title('Requisition submitted')->success()->send();
                    $this->refreshFormData(['status']);
                }),
            ...parent::getHeaderActions(),
        ];
    }
}
