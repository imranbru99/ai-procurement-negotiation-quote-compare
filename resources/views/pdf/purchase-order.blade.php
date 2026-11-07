<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order {{ $purchaseOrder->number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h1>Purchase Order {{ $purchaseOrder->number }}</h1>
    <p><strong>Supplier:</strong> {{ $purchaseOrder->supplier?->name }}</p>
    <p><strong>Status:</strong> {{ $purchaseOrder->status }}</p>
    <p><strong>Currency:</strong> {{ $purchaseOrder->currency_code }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit price</th>
                <th>Line total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseOrder->lines as $line)
                <tr>
                    <td>{{ $line->line_number }}</td>
                    <td>{{ $line->description }}</td>
                    <td>{{ $line->qty }}</td>
                    <td>{{ $line->unit_price }}</td>
                    <td>{{ number_format((float) $line->qty * (float) $line->unit_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total:</strong> {{ number_format((float) $purchaseOrder->total, 2) }} {{ $purchaseOrder->currency_code }}</p>
</body>
</html>
