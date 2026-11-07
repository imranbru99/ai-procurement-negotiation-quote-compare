<x-filament-panels::page>
    @if (config('ai.demo_mode'))
        <div class="mb-4 rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Demo AI mode is active — responses are deterministic and require human confirmation.
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-3">
        <x-filament::section heading="RFQ" class="lg:col-span-1">
            <select wire:model.live="rfqId" class="w-full rounded-lg border-gray-300">
                <option value="">Select RFQ</option>
                @foreach (\App\Models\Rfq::query()->latest()->limit(50)->get() as $rfq)
                    <option value="{{ $rfq->id }}">{{ $rfq->number }} — {{ $rfq->title }}</option>
                @endforeach
            </select>
        </x-filament::section>

        <x-filament::section heading="Weights" class="lg:col-span-2">
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($weights as $key => $value)
                    <label class="text-sm font-medium text-gray-700">
                        {{ str($key)->headline() }}
                        <input type="range" min="0" max="100" wire:model.live="weights.{{ $key }}" class="mt-1 w-full" />
                        <span class="text-xs text-gray-500">{{ $value }}%</span>
                    </label>
                @endforeach
            </div>
            <x-filament::button wire:click="saveComparison" class="mt-4">Save comparison</x-filament::button>
        </x-filament::section>
    </div>

    @if ($results)
        <x-filament::section heading="Matrix" class="mt-6">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="px-2 py-2 text-left">Supplier</th>
                            <th class="px-2 py-2 text-right">TCO (base)</th>
                            <th class="px-2 py-2 text-right">Score</th>
                            <th class="px-2 py-2 text-left">Flags</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($results['quotes'] ?? [] as $row)
                            <tr @class([
                                'border-b',
                                'bg-emerald-50' => ($results['best_value_quote_id'] ?? null) === ($row['quote_id'] ?? null),
                            ])>
                                <td class="px-2 py-2">{{ $row['supplier_name'] ?? '—' }}</td>
                                <td class="px-2 py-2 text-right">{{ number_format($row['tco_base'] ?? 0, 2) }}</td>
                                <td class="px-2 py-2 text-right">{{ $row['weighted_score'] ?? 0 }}</td>
                                <td class="px-2 py-2">
                                    @if ($row['outliers']['is_outlier'] ?? false)
                                        <span class="text-amber-700">Outlier</span>
                                    @endif
                                    @if (($results['best_value_quote_id'] ?? null) === ($row['quote_id'] ?? null))
                                        <span class="text-emerald-700">Best value</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
