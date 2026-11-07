<x-filament-panels::page>
    <div class="space-y-3">
        @forelse ($notifications as $notification)
            <div class="rounded-lg border border-gray-200 p-4 text-sm dark:border-gray-700">
                <div class="font-medium">{{ class_basename($notification->type) }}</div>
                <pre class="mt-2 overflow-x-auto text-xs">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                <div class="mt-1 text-gray-500">{{ $notification->created_at }}</div>
            </div>
        @empty
            <p class="text-sm text-gray-500">No notifications yet.</p>
        @endforelse
    </div>
</x-filament-panels::page>
