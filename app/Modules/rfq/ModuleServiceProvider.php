<?php

namespace App\Modules\rfq;

use App\Http\Controllers\RfqInvitationController;
use App\Models\Rfq;
use App\Modules\rfq\Services\RfqService;
use App\Policies\RfqPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RfqService::class);
    }

    public function boot(): void
    {
        Gate::policy(Rfq::class, RfqPolicy::class);

        Route::middleware(['web'])
            ->group(function (): void {
                Route::get('/rfq/invitations/{invitation}', RfqInvitationController::class)
                    ->name('rfq.invitation.show');
            });
    }
}
