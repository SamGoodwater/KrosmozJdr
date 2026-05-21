<!DOCTYPE html>
<html data-theme="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="@yield('keywords')" />
    <link rel="icon" href="{{ asset('storage/images/logos/logo_mini.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('storage/images/logos/logo_mini.webp') }}" type="image/webp">
    <link rel="icon" href="{{ asset('storage/images/logos/logo_mini.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('storage/images/logos/logo_mini.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('storage/images/logos/logo_mini.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('storage/images/logos/logo_mini.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=albert-sans:400,600,700" rel="stylesheet" />

    <!-- Scripts (Ziggy via prop Inertia shareOnce — pas de @routes) -->
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
    <link rel="stylesheet" href="{{ asset('css/characteristic-colors.css') }}" />
</head>

<body class="antialiased w-screen h-screen overflow-hidden">
    <a href="#main-content" class="skip-to-main">Aller au contenu principal</a>
    @inertia
</body>

</html>
