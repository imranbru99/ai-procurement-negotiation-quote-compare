<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\AppPanelProvider;
use App\Providers\Filament\SupplierPanelProvider;
use App\Providers\ModuleServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    ModuleServiceProvider::class,
    AdminPanelProvider::class,
    AppPanelProvider::class,
    SupplierPanelProvider::class,
];
