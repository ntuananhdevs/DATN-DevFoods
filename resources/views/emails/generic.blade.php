<!DOCTYPE html>
<html>
<head>
    <title>{{ $subjectLine ?? 'Mail' }}</title>
</head>
<body>
    <div>
        {!! $content !!}
    </div>

    @if(!empty($data))
        <ul>
            @foreach($data as $key => $value)
                <li><strong>{{ $key }}:</strong> {{ $value }}</li>
            @endforeach
        </ul>
    @endif
</body>
</html>
