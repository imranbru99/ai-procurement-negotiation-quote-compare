<?php

namespace App\Filament\App\Pages;

use App\Facades\Module;
use App\Modules\spend_analytics\Services\SpendAnalyticsService;
use BackedEnum;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class SpendAnalyticsDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $navigationLabel = 'Spend analytics';

    protected static ?string $title = 'Spend analytics';

    protected static string|\UnitEnum|null $navigationGroup = 'Intelligence';

    protected static ?int $navigationSort = 40;

    protected string $view = 'filament.app.pages.spend-analytics-dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return Module::enabled('spend_analytics');
    }

    #[Computed]
    public function snapshot(): array
    {
        return app(SpendAnalyticsService::class)->snapshot();
    }
}
