<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status }} - {{ $title }}</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen w-full bg-base-200 text-base-content">
    <div class="mx-auto flex min-h-screen w-full max-w-4xl items-center justify-center p-6">
        <div class="card w-full border border-base-300 bg-base-100 shadow-2xl">
            <div class="card-body items-center text-center">
                <p class="text-sm font-semibold uppercase tracking-widest text-primary">Erreur HTTP</p>
                <h1 class="text-5xl font-black text-primary">{{ $status }}</h1>
                <h2 class="text-2xl font-bold">{{ $title }}</h2>
                <p class="max-w-2xl text-base-content/80">{{ $description }}</p>
                @if (!empty($hint))
                    <p class="alert alert-info mt-2 max-w-2xl text-sm">{{ $hint }}</p>
                @endif

                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <a class="btn btn-primary" href="{{ url('/') }}">Retour à l'accueil</a>
                    <a class="btn btn-ghost" href="{{ url()->previous() }}">Revenir à la page précédente</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
