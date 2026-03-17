<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #222; }
        h1 { margin: 0 0 8px; font-size: 24px; }
        .meta { margin-bottom: 20px; color: #666; font-size: 13px; }
        h2 { margin-top: 28px; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d8d8d8; padding: 8px 10px; text-align: left; }
        th { background: #f4f6f8; }
    </style>
</head>
<body onload="window.print()">
    <h1>{{ $title }}</h1>
    <div class="meta">
        {{ __('From') }}: {{ $filters['from'] ?? '-' }} |
        {{ __('To') }}: {{ $filters['to'] ?? '-' }}
    </div>

    @foreach ($sections as $section)
        <h2>{{ __($section['title']) }}</h2>
        <table>
            @if (!empty($section['headers']))
                <thead>
                    <tr>
                        @foreach ($section['headers'] as $header)
                            <th>{{ __($header) }}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody>
                @forelse ($section['rows'] as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($section['headers'] ?? [1]) }}">{{ __('No data found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</body>
</html>
