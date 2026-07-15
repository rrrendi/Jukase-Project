<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Jukase Project') }} — Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Hanken+Grotesk:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="jk">
    <div class="auth-wrap">
        <div class="auth-card">
            <a href="{{ route('home') }}" class="wordmark auth-logo">
                <span class="sq"><b>J</b></span>JUKASE<span style="color:var(--volt-deep)">·</span>
            </a>
            <div class="auth-eyebrow mono">Admin Panel</div>
            {{ $slot }}
        </div>
    </div>
</body>
</html>