@extends('emails.layout')

@section('title', 'Vérifie ton adresse email')

@section('content')
<p>Bonjour {{ $userName }},</p>

<p>Merci pour ton inscription sur {{ config('app.name') }}. Clique sur le bouton ci-dessous pour vérifier ton adresse email.</p>

<p style="margin: 1.5rem 0;">
    <a href="{{ $verificationUrl }}" class="btn">Vérifier mon email</a>
</p>

<p>Si le bouton ne fonctionne pas, copie et colle ce lien dans ton navigateur :</p>
<p><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>

<p>Ce lien expire dans 60 minutes. Si tu n'as pas créé de compte, tu peux ignorer cet email.</p>
@endsection
