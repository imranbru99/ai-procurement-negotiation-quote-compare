<?php

namespace App\Filament\App\Pages;

use App\Facades\Module;
use App\Services\Settings\SettingsService;
use App\Services\Tenancy\TenantContext;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class MultiLanguageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $navigationLabel = 'Languages';

    protected static ?string $title = 'Multi-language settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Platform';

    protected static ?int $navigationSort = 65;

    protected string $view = 'filament.app.pages.multi-language-settings';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return Module::enabled('multi_language');
    }

    public function mount(SettingsService $settings): void
    {
        abort_unless(TenantContext::getId(), 403);

        $available = config('public.locales', []);
        $savedLocales = $settings->get('multi_language', 'available_locales', array_keys($available));
        $defaultLocale = $settings->get('multi_language', 'default_locale', config('public.default_locale', 'en'));

        $this->form->fill([
            'available_locales' => is_array($savedLocales) ? $savedLocales : array_keys($available),
            'default_locale' => is_string($defaultLocale) ? $defaultLocale : 'en',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $localeOptions = collect(config('public.locales', []))
            ->mapWithKeys(fn (array $meta, string $code): array => [$code => ($meta['name'] ?? $code).' ('.$code.')'])
            ->all();

        return $schema
            ->components([
                CheckboxList::make('available_locales')
                    ->label('Available locales')
                    ->options($localeOptions)
                    ->columns(2)
                    ->required(),
                Select::make('default_locale')
                    ->label('Default locale')
                    ->options($localeOptions)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(SettingsService $settings): void
    {
        $state = $this->form->getState();
        $locales = $state['available_locales'] ?? [];
        $default = $state['default_locale'] ?? 'en';

        if (! in_array($default, $locales, true)) {
            $locales[] = $default;
        }

        $settings->set('multi_language', 'available_locales', array_values($locales));
        $settings->set('multi_language', 'default_locale', $default);

        $tenant = TenantContext::get();
        if ($tenant) {
            $tenant->forceFill(['locale' => $default])->save();
        }

        Notification::make()->title('Language settings saved')->success()->send();
    }
}
