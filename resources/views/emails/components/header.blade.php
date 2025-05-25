<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .content {
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <a href="{{ config('app.url') }}">
                <img src="{{ config('app.url') }}/images/logo.png" alt="{{ config('app.name') }}" class="logo">
            </a>
            <h2>{{ $subjectLine ?? 'Thông báo' }}</h2>
        </div>
        <div class="content"> 