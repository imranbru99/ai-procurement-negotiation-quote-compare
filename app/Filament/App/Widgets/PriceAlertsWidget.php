<?php

namespace App\Filament\App\Widgets;

use App\Facades\Module;
use App\Models\PriceAlert;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PriceAlertsWidget extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return Module::enabled('price_intelligence');
    }

    protected function getStats(): array
    {
        $alerts = PriceAlert::query()->count();
        $recent = PriceAlert::query()->whereNotNull('last_triggered_at')->where('last_triggered_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Price alerts', (string) $alerts),
            Stat::make('Triggered (7d)', (string) $recent),
        ];
    }
}
