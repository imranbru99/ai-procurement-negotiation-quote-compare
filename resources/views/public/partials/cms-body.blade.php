@if ($page?->renderedBody())
    <div class="prose-public prose prose-slate max-w-none dark:prose-invert">
        {!! $page->renderedBody() !!}
    </div>
@elseif (! empty($fallback))
    <p class="text-lg text-slate-600 dark:text-slate-300">{{ $fallback }}</p>
@endif
