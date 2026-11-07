New contact form submission

Name: {{ $payload['name'] }}
Email: {{ $payload['email'] }}
Company: {{ $payload['company'] ?? '—' }}

Message:
{{ $payload['message'] }}
