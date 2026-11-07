<?php

namespace Database\Seeders;

use App\Facades\Module;
use App\Models\User;
use App\Modules\attendance\Database\Seeders\AttendanceModuleSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(AiPromptTemplateSeeder::class);

        Module::syncFromManifests();

        $this->call(PlanSeeder::class);
        $this->call(CmsSeeder::class);
        $this->call(DemoTenantsSeeder::class);
        $this->call(AttendanceModuleSeeder::class);

        User::query()->updateOrCreate(
            ['email' => 'superadmin@procureai.test'],
            [
                'name' => 'Platform Super Admin',
                'password' => Hash::make('password'),
                'type' => 'platform',
                'tenant_id' => null,
                'locale' => 'en',
                'timezone' => 'UTC',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        )->assignRole('super_admin');
    }
}
