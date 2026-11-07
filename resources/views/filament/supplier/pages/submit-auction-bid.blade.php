<x-filament-panels::page wire:poll.5s>
    <form wire:submit="placeBid" class="space-y-4">
        {{ $this->form }}

        <x-filament::button type="submit">Place bid</x-filament::button>
    </form>

    @if (($data['auction_id'] ?? null))
        <x-filament::section heading="Live bids" class="mt-6">
            <ul class="divide-y text-sm">
                @foreach ($this->recentBids as $bid)
                    <li class="flex justify-between py-2">
                        <span>{{ number_format((float) $bid->amount, 2) }} @if($bid->is_winning) (leading) @endif</span>
                        <span>{{ $bid->placed_at?->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
        </x-filament::section>
    @endif
</x-filament-panels::page>
