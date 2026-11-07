<?php

namespace App\Services\Public;

use App\Models\Tenant;
use App\Models\User;
use App\Modules\suppliers\Enums\SupplierStatus;
use App\Modules\suppliers\Enums\SupplierTier;
use App\Modules\suppliers\Models\Supplier;
use App\Modules\suppliers\Models\SupplierUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class SupplierSignupService
{
    /**
     * @param  array{
     *     company_name: string,
     *     contact_name: string,
     *     email: string,
     *     phone?: string|null,
     *     country?: string|null,
     *     password: string,
     *     tenant_slug?: string|null,
     *     notes?: string|null
     * }  $data
     * @return array{supplier: Supplier, user: User}
     */
    public function register(array $data): array
    {
        $tenant = $this->resolveTenant($data['tenant_slug'] ?? null);

        return DB::transaction(function () use ($data, $tenant): array {
            $supplier = Supplier::query()->withoutGlobalScopes()->create([
                'tenant_id' => $tenant->id,
                'name' => $data['company_name'],
                'slug' => Str::slug($data['company_name']).'-'.Str::lower(Str::random(6)),
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'country' => isset($data['country']) ? Str::upper($data['country']) : null,
                'currency_code' => $tenant->currency ?: 'USD',
                'tier' => SupplierTier::Standard->value,
                'status' => SupplierStatus::Pending->value,
                'onboarding_notes' => $data['notes'] ?? 'Public supplier signup — pending review.',
            ]);

            $user = User::query()->create([
                'tenant_id' => $tenant->id,
                'supplier_id' => $supplier->id,
                'type' => 'supplier',
                'name' => $data['contact_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'locale' => $tenant->locale ?: 'en',
                'timezone' => $tenant->timezone ?: 'UTC',
                'is_active' => false,
                'email_verified_at' => null,
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('supplier');
            }

            SupplierUser::query()->create([
                'user_id' => $user->id,
                'supplier_id' => $supplier->id,
            ]);

            return compact('supplier', 'user');
        });
    }

    protected function resolveTenant(?string $slug): Tenant
    {
        $query = Tenant::query()->where('is_active', true);

        if ($slug) {
            $tenant = $query->where('slug', $slug)->first();
            if ($tenant) {
                return $tenant;
            }
        }

        $tenant = $query->orderBy('id')->first();

        if (! $tenant) {
            throw new RuntimeException('No active tenant is available for supplier signup.');
        }

        return $tenant;
    }
}
