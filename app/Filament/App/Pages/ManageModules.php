<?php

namespace App\Filament\App\Pages;

use App\Facades\Module;
use App\Models\ModuleDefinition;
use App\Services\Tenancy\TenantContext;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use RuntimeException;

class ManageModules extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Modules';

    protected static ?int $navigationSort = 90;

    protected static ?string $title = 'Module Manager';

    protected string $view = 'filament.pages.manage-modules';

    public function mount(): void
    {
        abort_unless(TenantContext::getId(), 403);
    }

    public function getHeading(): string|Htmlable
    {
        return 'Tenant Module Manager';
    }

    /**
     * @return Collection<int, ModuleDefinition>
     */
    #[Computed]
    public function modules()
    {
        return Module::forTenant(TenantContext::getId())->sortBy('sort')->values();
    }

    public function toggleModule(string $key): void
    {
        $tenantId = TenantContext::getId();

        try {
            if (Module::enabled($key, $tenantId)) {
                Module::disable($key, $tenantId);
                Notification::make()->title("Module [{$key}] disabled")->success()->send();
            } else {
                Module::enable($key, $tenantId);
                Notification::make()->title("Module [{$key}] enabled")->success()->send();
            }
        } catch (RuntimeException $exception) {
            Notification::make()
                ->title('Unable to update module')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }

        unset($this->modules);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user
            && $user->type === 'tenant'
            && $user->tenant_id
            && ($user->hasRole('tenant_owner') || $user->hasRole('procurement_manager') || $user->can('modules.manage'));
    }
}
