<x-filament-panels::page>
    @php($data = $this->snapshot)
    <div class="grid gap-4 md:grid-cols-3">
        <x-filament::section>
            <x-slot name="heading">Total spend</x-slot>
            <p class="text-2xl font-semibold">{{ number_format($data['total_spend'] ?? 0, 2) }}</p>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">Maverick spend</x-slot>
            <p class="text-2xl font-semibold">{{ number_format($data['maverick_spend'] ?? 0, 2) }}</p>
            <p class="text-sm text-gray-500">Single PO &lt; 5k heuristic</p>
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">Tail suppliers</x-slot>
            <p class="text-sm">{{ implode(', ', $data['tail_suppliers'] ?? []) ?: '—' }}</p>
        </x-filament::section>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-filament::section heading="By supplier">
            <ul class="divide-y">
                @foreach ($data['by_supplier'] ?? [] as $row)
                    <li class="flex justify-between py-2 text-sm">
                        <span>{{ $row['name'] }}</span>
                        <span>{{ number_format($row['total'], 2) }}</span>
                    </li>
                @endforeach
            </ul>
        </x-filament::section>
        <x-filament::section heading="By department">
            <ul class="divide-y">
                @foreach ($data['by_department'] ?? [] as $row)
                    <li class="flex justify-between py-2 text-sm">
                        <span>{{ $row['name'] }}</span>
                        <span>{{ number_format($row['total'], 2) }}</span>
                    </li>
                @endforeach
            </ul>
        </x-filament::section>
    </div>
</x-filament-panels::page>
