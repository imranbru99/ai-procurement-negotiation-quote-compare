<?php

namespace App\Services\Public;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class ModuleManifestCatalog
{
    /**
     * @return Collection<int, array{key: string, name: string, description: string, icon: string, category: string}>
     */
    public function all(): Collection
    {
        $modules = collect();

        foreach (File::glob(app_path('Modules/*/module.json')) ?: [] as $path) {
            $data = json_decode((string) File::get($path), true);
            if (! is_array($data) || empty($data['key'])) {
                continue;
            }

            $modules->push([
                'key' => (string) $data['key'],
                'name' => (string) ($data['name'] ?? $data['key']),
                'description' => (string) ($data['description'] ?? ''),
                'icon' => (string) ($data['icon'] ?? 'heroicon-o-cube'),
                'category' => (string) ($data['category'] ?? 'procurement'),
            ]);
        }

        return $modules->sortBy('name')->values();
    }

    public function find(string $key): ?array
    {
        return $this->all()->firstWhere('key', $key);
    }
}
