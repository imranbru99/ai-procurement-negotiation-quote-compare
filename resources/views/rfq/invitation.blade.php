<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invitation->rfq->title }} — RFQ Invitation</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; color: #1f2937; }
        h1 { margin-bottom: 0.25rem; }
        .meta { color: #6b7280; margin-bottom: 1.5rem; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: left; }
        th { background: #f9fafb; }
    </style>
</head>
<body>
    <h1>{{ $invitation->rfq->title }}</h1>
    <p class="meta">RFQ {{ $invitation->rfq->number }} · {{ $invitation->supplier->name }}</p>
    @if ($invitation->rfq->description)
        <p>{{ $invitation->rfq->description }}</p>
    @endif
    <h2>Line items</h2>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invitation->rfq->lines as $line)
                <tr>
                    <td>{{ $line->description }}</td>
                    <td>{{ $line->qty }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="meta">Respond via the supplier portal to submit your quote.</p>
</body>
</html>
