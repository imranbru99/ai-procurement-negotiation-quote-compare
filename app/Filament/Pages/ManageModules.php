<?php

namespace App\Filament\Pages;

use App\Facades\Module;
use App\Models\ModuleDefinition;
use App\Models\Tenant;
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

    public ?int $tenantId = null;

    public function mount(): void
    {
        $this->tenantId = request()->integer('tenant');
    }

    public function getHeading(): string|Htmlable
    {
        return 'ProcureAI Module Manager';
    }

    /**
     * @return Collection<int, ModuleDefinition>
     */
    #[Computed]
    public function modules()
    {
        return Module::forTenant($this->tenantId)->sortBy('sort')->values();
    }

    /**
     * @return Collection<int, Tenant>
     */
    #[Computed]
    public function tenants()
    {
        return Tenant::query()->orderBy('name')->get();
    }

    public function toggleModule(string $key): void
    {
        if ($this->tenantId === null) {
            Notification::make()
                ->title('Select a tenant')
                ->body('Choose a tenant before enabling or disabling modules.')
                ->warning()
                ->send();

            return;
        }

        try {
            if (Module::enabled($key, $this->tenantId)) {
                Module::disable($key, $this->tenantId);
                Notification::make()->title("Module [{$key}] disabled")->success()->send();
            } else {
                Module::enable($key, $this->tenantId);
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

        return $user && $user->type === 'platform' && $user->hasRole('super_admin');
    }
}
