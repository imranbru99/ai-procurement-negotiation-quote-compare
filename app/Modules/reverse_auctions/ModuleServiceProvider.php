<?php

namespace App\Modules\reverse_auctions;

use App\Facades\Module;
use App\Modules\reverse_auctions\Services\AuctionService;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AuctionService::class);
    }

    public function boot(): void
    {
        if (! Module::enabled('reverse_auctions')) {
            return;
        }
    }
}
