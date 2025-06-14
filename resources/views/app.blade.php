<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link href="https://fonts.bunny.net/css?family=albert-sans:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
    @vite('resources/css/app.css')
</head>

<body class="antialiased w-screen h-screen" style="margin-bottom:0px!important;">
    @inertia
</body>

</html>
