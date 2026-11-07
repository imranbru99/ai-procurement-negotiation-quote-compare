<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuleDefinition extends Model
{
    protected $table = 'modules';

    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'category',
        'depends_on',
        'is_core',
        'default_enabled',
        'sort',
        'permissions',
        'navigation',
    ];

    protected function casts(): array
    {
        return [
            'depends_on' => 'array',
            'is_core' => 'boolean',
            'default_enabled' => 'boolean',
            'permissions' => 'array',
            'navigation' => 'array',
        ];
    }

    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class, 'module_id');
    }
}
