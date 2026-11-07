<x-filament-panels::page wire:poll.5s>
    <x-filament::section>
        <x-slot name="heading">{{ $record->title }}</x-slot>
        <p class="text-sm">Status: <strong>{{ $record->status }}</strong> · Ends: {{ $record->ends_at?->toDateTimeString() }}</p>
        <p class="mt-2 text-sm">
            Winning bid:
            {{ optional($record->winningBid())->amount ?? '—' }}
            {{ $record->currency_code }}
        </p>
    </x-filament::section>

    <x-filament::section heading="Recent bids" class="mt-4">
        <ul class="divide-y">
            @forelse ($this->bids as $bid)
                <li class="flex justify-between py-2 text-sm">
                    <span>{{ $bid->supplier?->name }} @if($bid->is_winning) (winning) @endif</span>
                    <span>{{ number_format((float) $bid->amount, 2) }} · {{ $bid->placed_at?->diffForHumans() }}</span>
                </li>
            @empty
                <li class="py-2 text-sm text-gray-500">No bids yet.</li>
            @endforelse
        </ul>
    </x-filament::section>
</x-filament-panels::page>
