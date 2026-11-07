<?php

namespace App\Filament\App\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationCenter extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static ?string $navigationLabel = 'Notifications';

    protected static string|\UnitEnum|null $navigationGroup = 'Platform';

    protected string $view = 'filament.app.pages.notification-center';

    /** @var Collection<int, DatabaseNotification> */
    public $notifications;

    public function mount(): void
    {
        $this->notifications = auth()->user()->notifications()->latest()->limit(50)->get();
    }
}
