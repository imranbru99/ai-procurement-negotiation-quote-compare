<?php

namespace App\Modules\backup_gdpr\Console;

use App\Models\Tenant;
use App\Modules\backup_gdpr\Services\GdprService;
use Illuminate\Console\Command;

class ExportTenantGdprCommand extends Command
{
    protected $signature = 'gdpr:export-tenant {tenant_id : Tenant primary key}';

    protected $description = 'Export tenant-owned data to a ZIP archive of JSON files';

    public function handle(GdprService $gdprService): int
    {
        $tenant = Tenant::query()->findOrFail($this->argument('tenant_id'));
        $path = $gdprService->exportTenantData($tenant);

        $this->info("GDPR export written to: {$path}");

        return self::SUCCESS;
    }
}
