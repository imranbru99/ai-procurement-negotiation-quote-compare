<?php

namespace App\Filament\App\Resources\GdprDeleteRequests\Pages;

use App\Filament\App\Resources\GdprDeleteRequests\GdprDeleteRequestResource;
use App\Modules\backup_gdpr\Services\GdprService;
use App\Services\Tenancy\TenantContext;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageGdprDeleteRequests extends ManageRecords
{
    protected static string $resource = GdprDeleteRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('requestExport')
                ->label('Export tenant data')
                ->action(function (GdprService $gdpr): void {
                    $tenant = TenantContext::get();
                    abort_unless($tenant, 403);

                    $path = $gdpr->exportTenantData($tenant);

                    Notification::make()
                        ->title('GDPR export ready')
                        ->body(basename($path))
                        ->success()
                        ->send();
                }),
            Action::make('requestDelete')
                ->label('Request my deletion')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (GdprService $gdpr): void {
                    $tenant = TenantContext::get();
                    $user = Auth::user();
                    abort_unless($tenant && $user, 403);

                    $gdpr->recordDeleteRequest($tenant, $user);

                    Notification::make()
                        ->title('Deletion request recorded')
                        ->success()
                        ->send();
                }),
        ];
    }
}
