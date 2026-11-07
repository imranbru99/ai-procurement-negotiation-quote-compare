<x-filament-panels::page>
    <x-filament::section heading="Raw text">
        <pre class="whitespace-pre-wrap text-sm">{{ $record->raw_text }}</pre>
    </x-filament::section>

    <x-filament::section heading="Extracted lines" class="mt-4">
        <pre class="whitespace-pre-wrap text-sm">{{ json_encode($record->extracted, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </x-filament::section>
</x-filament-panels::page>
