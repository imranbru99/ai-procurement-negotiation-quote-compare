<x-mail::message>
# Purchase Order {{ $purchaseOrder->number }}

You have received purchase order **{{ $purchaseOrder->number }}** for **{{ number_format((float) $purchaseOrder->total, 2) }} {{ $purchaseOrder->currency_code }}**.

Please acknowledge in the supplier portal.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
