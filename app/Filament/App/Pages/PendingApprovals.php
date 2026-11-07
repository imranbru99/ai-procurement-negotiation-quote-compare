<?php

namespace App\Filament\App\Pages;

use App\Facades\Module;
use App\Models\ApprovalRequest;
use App\Modules\approvals\Services\ApprovalEngine;
use App\Services\Tenancy\TenantContext;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PendingApprovals extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static string|\UnitEnum|null $navigationGroup = 'Sourcing';

    protected static ?string $navigationLabel = 'Pending approvals';

    protected static ?int $navigationSort = 15;

    protected string $view = 'filament.app.pages.pending-approvals';

    public static function shouldRegisterNavigation(): bool
    {
        return Module::enabled('approvals', TenantContext::getId());
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->can('approvals.view') || $user->can('approvals.manage'));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ApprovalRequest::query()
                    ->where('status', ApprovalRequest::STATUS_PENDING)
            )
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('approvable_type')->label('Entity'),
                TextColumn::make('current_level'),
                TextColumn::make('workflow.name'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->action(function (ApprovalRequest $record, ApprovalEngine $engine): void {
                        $engine->approve($record, Auth::user());
                        Notification::make()->title('Approved')->success()->send();
                    }),
                Action::make('reject')
                    ->color('danger')
                    ->action(function (ApprovalRequest $record, ApprovalEngine $engine): void {
                        $engine->reject($record, Auth::user());
                        Notification::make()->title('Rejected')->success()->send();
                    }),
            ]);
    }
}
