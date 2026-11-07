<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $rfq->number }}</title>
</head>
<body>
    <h1>{{ $rfq->title }}</h1>
    <p>RFQ {{ $rfq->number }}</p>
    @if ($rfq->description)
        <p>{{ $rfq->description }}</p>
    @endif
    <ul>
        @foreach ($rfq->lines as $line)
            <li>{{ $line->description }} — {{ $line->qty }}</li>
        @endforeach
    </ul>
</body>
</html>
