@extends('emails.layout')

@section('title')
[{{ config('app.name') }}] Copie de ton retour — {{ $typeLabel ?? 'Autre' }}
@endsection

@section('content')
<p>Bonjour,</p>

<p>Voici une copie du retour que tu viens d'envoyer aux administrateurs de {{ config('app.name') }}.</p>

<p><strong>Type :</strong> {{ $typeLabel ?? 'Autre' }}</p>

@if(!empty($url))
<p><strong>URL :</strong> <a href="{{ $url }}">{{ $url }}</a></p>
@endif

<p><strong>Message :</strong></p>
<p>{{ $feedbackMessage ?? '-' }}</p>

<p class="text-sm opacity-80">Tu as demandé à recevoir ce récapitulatif par email. Les administrateurs traiteront ton retour séparément.</p>
@endsection
