@extends('public.layouts.marketing')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-16 lg:px-8">
        <h1 class="font-display text-4xl font-semibold">{{ __('public.nav.quote_comparison') }}</h1>
        <p class="mt-4 text-slate-600 dark:text-slate-300">{{ __('public.quote_demo.lead') }}</p>

        <div class="mt-10 overflow-x-auto rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-950/60">
                    <tr>
                        <th class="px-4 py-3 text-start font-semibold">{{ __('public.quote_demo.line_item') }}</th>
                        @foreach ($demo['suppliers'] as $supplier)
                            <th class="px-4 py-3 text-end font-semibold">{{ $supplier }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($demo['line_items'] as $row => $line)
                        @php $rowLowest = min($demo['matrix'][$row]); @endphp
                        <tr class="border-t border-slate-200 dark:border-slate-800">
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $line['description'] }}</p>
                                <p class="text-xs text-slate-500">{{ $line['sku'] }}</p>
                            </td>
                            @foreach ($demo['suppliers'] as $col => $supplier)
                                @php $value = $demo['matrix'][$row][$col]; @endphp
                                <td @class([
                                    'px-4 py-3 text-end font-mono',
                                    'font-semibold text-brand-700 dark:text-brand-300' => $value === $rowLowest,
                                ])>
                                    ${{ number_format($value, 2) }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
