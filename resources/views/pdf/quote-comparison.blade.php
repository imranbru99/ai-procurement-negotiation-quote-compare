<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Quote comparison' }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'Quote comparison' }}</h1>
    <p>সরবরাহকারী তুলনা প্রতিবেদন — ProcureAI</p>

    @if (! empty($results['quotes']))
        <table>
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>TCO</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results['quotes'] as $row)
                    <tr>
                        <td>{{ $row['supplier_name'] ?? '—' }}</td>
                        <td>{{ number_format($row['tco_base'] ?? 0, 2) }}</td>
                        <td>{{ $row['weighted_score'] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No comparison data.</p>
    @endif
</body>
</html>
