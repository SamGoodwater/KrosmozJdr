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
    <link rel="preload" href="{{ asset('storage/images/backgrounds/loading.webp') }}" as="image" type="image/webp" />
    <style>
        @keyframes site-loading-boot-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.55); }
        }
        @keyframes site-loading-boot-content-enter {
            from { opacity: 0; transform: translateY(1.25rem) scale(0.88); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes site-loading-boot-dot {
            0%, 80%, 100% { opacity: 0.35; transform: scale(0.85); }
            40% { opacity: 1; transform: scale(1); }
        }
        #site-loading-boot {
            position: fixed;
            inset: 0;
            z-index: 10050;
            overflow: hidden;
            background: #000;
        }
        #site-loading-boot .site-loading-boot__stage {
            position: absolute;
            inset: 0;
            transform-origin: center center;
            animation: site-loading-boot-pulse 10s ease-in-out infinite;
        }
        #site-loading-boot img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        #site-loading-boot .site-loading-boot__content {
            position: absolute;
            inset: 0;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-family: "Albert Sans", system-ui, sans-serif;
            color: #fff;
            pointer-events: none;
            line-height: 1;
            animation: site-loading-boot-content-enter 1s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }
        #site-loading-boot .site-loading-boot__title-krosmoz {
            font-size: clamp(3.5rem, 14vw, 6.5rem);
            font-weight: 700;
            letter-spacing: 0.04em;
            text-shadow: 0 2px 28px rgba(0, 0, 0, 0.9);
        }
        #site-loading-boot .site-loading-boot__title-jdr {
            margin-top: 0.65rem;
            font-size: clamp(2.25rem, 8vw, 4rem);
            font-weight: 600;
            letter-spacing: 0.35em;
            text-shadow: 0 2px 22px rgba(0, 0, 0, 0.85);
        }
        #site-loading-boot .site-loading-boot__status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            margin-top: 2.25rem;
            font-size: clamp(1.25rem, 4vw, 1.75rem);
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
        }
        #site-loading-boot .site-loading-boot__dots {
            display: inline-flex;
            gap: 0.2rem;
            align-items: flex-end;
            height: 1.1em;
        }
        #site-loading-boot .site-loading-boot__dots i {
            display: block;
            width: 0.35em;
            height: 0.35em;
            border-radius: 9999px;
            background: currentColor;
            animation: site-loading-boot-dot 1.2s ease-in-out infinite;
        }
        #site-loading-boot .site-loading-boot__dots i:nth-child(2) { animation-delay: 0.15s; }
        #site-loading-boot .site-loading-boot__dots i:nth-child(3) { animation-delay: 0.3s; }
        @media (prefers-reduced-motion: reduce) {
            #site-loading-boot .site-loading-boot__stage {
                animation: none;
                transform: scale(1.06);
            }
            #site-loading-boot .site-loading-boot__content {
                animation: none;
                opacity: 1;
                transform: none;
            }
            #site-loading-boot .site-loading-boot__dots i {
                animation: none;
                opacity: 1;
            }
        }
    </style>

    <!-- Scripts (Ziggy via prop Inertia shareOnce — pas de @routes) -->
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
    <link rel="stylesheet" href="{{ asset('css/characteristic-colors.css') }}" />
</head>

<body class="antialiased w-screen h-screen overflow-hidden">
    <div id="site-loading-boot" aria-hidden="true">
        <div class="site-loading-boot__stage">
            <img src="{{ asset('storage/images/backgrounds/loading.webp') }}"
                onerror="this.onerror=null;this.src='{{ asset('storage/images/backgrounds/loading.png') }}';"
                alt="" />
        </div>
        <div class="site-loading-boot__content">
            <span class="site-loading-boot__title-krosmoz">Krosmoz</span>
            <span class="site-loading-boot__title-jdr">JDR</span>
            <p class="site-loading-boot__status">
                <span>Chargement</span>
                <span class="site-loading-boot__dots" aria-hidden="true"><i></i><i></i><i></i></span>
            </p>
        </div>
    </div>
    <a href="#main-content" class="skip-to-main">Aller au contenu principal</a>
    @inertia
</body>

</html>
