<?php

use App\Modules\inventory\Jobs\AutoRequisitionJob;
use App\Modules\multi_currency\Jobs\UpdateExchangeRatesJob;
use App\Modules\price_intelligence\Jobs\ScrapeMarketPricesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new UpdateExchangeRatesJob)->dailyAt('01:00');
Schedule::command('contracts:renewal-alerts')->daily();
Schedule::command('documents:expiry-alerts')->dailyAt('07:00');
Schedule::job(new AutoRequisitionJob)->daily();
Schedule::job(new ScrapeMarketPricesJob)->dailyAt('03:30');
Schedule::command('tasks:send-reminders')->hourly();
Schedule::command('demo:reset')->dailyAt('02:00')->environments(['local', 'staging']);
