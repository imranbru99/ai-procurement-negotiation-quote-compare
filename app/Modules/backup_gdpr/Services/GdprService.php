<?php

namespace App\Modules\backup_gdpr\Services;

use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use App\Modules\backup_gdpr\Models\GdprDeleteRequest;
use App\Modules\suppliers\Models\Supplier;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ZipArchive;

class GdprService
{
    public function exportTenantData(Tenant $tenant): string
    {
        $dir = 'gdpr-exports/'.$tenant->id.'/'.now()->format('Ymd_His');
        $absolute = storage_path('app/'.$dir);
        File::ensureDirectoryExists($absolute);

        $payloads = [
            'tenant.json' => $tenant->toArray(),
            'users.json' => User::query()->withoutGlobalScopes()->where('tenant_id', $tenant->id)->get()->toArray(),
            'settings.json' => Setting::query()->withoutGlobalScopes()->where('tenant_id', $tenant->id)->get()->toArray(),
            'suppliers.json' => Supplier::query()->withoutGlobalScopes()->where('tenant_id', $tenant->id)->get()->toArray(),
        ];

        foreach ($payloads as $filename => $data) {
            file_put_contents(
                $absolute.'/'.$filename,
                json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
            );
        }

        $zipRelative = $dir.'.zip';
        $zipPath = storage_path('app/'.$zipRelative);

        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach (File::files($absolute) as $file) {
            $zip->addFile($file->getPathname(), $file->getFilename());
        }

        $zip->close();

        return $zipPath;
    }

    public function anonymizeUser(User $user): User
    {
        $token = Str::random(8);

        $user->forceFill([
            'name' => 'Anonymized User',
            'email' => 'anonymized+'.$token.'@invalid.local',
            'is_active' => false,
            'password' => bcrypt(Str::random(32)),
        ])->save();

        return $user->fresh();
    }

    public function recordDeleteRequest(Tenant $tenant, User $user): GdprDeleteRequest
    {
        return GdprDeleteRequest::query()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'requested_at' => now(),
        ]);
    }
}
