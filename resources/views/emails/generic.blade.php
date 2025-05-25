<!DOCTYPE html>
<html>

<head>
    <title>{{ $subjectLine ?? 'Mail' }}</title>
</head>

<body>
    @include('emails.components.header')

    <div>
        {!! $content !!}
    </div>
    @if (!empty($data))
        <ul>
            @foreach ($data as $key => $value)
                @if ($key !== 'content')
                    <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                @endif
            @endforeach
        </ul>
    @endif

    @include('emails.components.footer')
</body>

</html>
