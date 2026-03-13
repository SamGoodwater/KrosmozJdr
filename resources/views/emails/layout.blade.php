<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', config('app.name'))</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; line-height: 1.6; color: #1a1a1a; max-width: 600px; margin: 0 auto; padding: 1rem; }
        .header { border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .footer { margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; font-size: 0.875rem; color: #6b7280; }
        a { color: #2563eb; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #2563eb; color: white !important; border-radius: 0.375rem; margin: 1rem 0; }
    </style>
</head>
<body>
    <div class="header">
        <strong>{{ config('app.name') }}</strong>
    </div>

    @yield('content')

    <div class="footer">
        @yield('footer', 'Ce message a été envoyé depuis ' . config('app.name') . '.')
    </div>
</body>
</html>
