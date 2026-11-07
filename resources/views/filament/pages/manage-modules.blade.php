<x-filament-panels::page>
    @if ($this instanceof \App\Filament\Pages\ManageModules)
        <div class="mb-6">
            <label for="tenantId" class="text-sm font-medium text-gray-700 dark:text-gray-200">Tenant</label>
            <select
                id="tenantId"
                wire:model.live="tenantId"
                class="mt-1 block w-full max-w-md rounded-lg border-gray-300 text-sm shadow-sm dark:border-gray-600 dark:bg-gray-800"
            >
                <option value="">Select tenant…</option>
                @foreach ($this->tenants as $tenant)
                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($this->modules as $module)
            @php
                $enabled = (bool) ($module->is_enabled_for_tenant ?? Module::enabled($module->key, $this->tenantId ?? \App\Services\Tenancy\TenantContext::getId()));
                $dependencies = Module::dependencies($module->key);
            @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $module->name }}</h3>
                        <p class="mt-1 text-xs uppercase tracking-wide text-gray-500">{{ $module->key }}</p>
                    </div>
                    @if ($module->is_core)
                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">
                            Core
                        </span>
                    @endif
                </div>

                <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ $module->description }}</p>

                @if (! empty($dependencies))
                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                        Depends on: {{ implode(', ', $dependencies) }}
                    </p>
                @endif

                <div class="mt-4 flex items-center justify-between">
                    <span class="text-sm {{ $enabled ? 'text-success-600' : 'text-gray-500' }}">
                        {{ $enabled ? 'Enabled' : 'Disabled' }}
                    </span>

                    @if (! $module->is_core && ($this->tenantId ?? \App\Services\Tenancy\TenantContext::getId()))
                        <x-filament::button
                            size="sm"
                            color="{{ $enabled ? 'danger' : 'primary' }}"
                            wire:click="toggleModule('{{ $module->key }}')"
                        >
                            {{ $enabled ? 'Disable' : 'Enable' }}
                        </x-filament::button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
