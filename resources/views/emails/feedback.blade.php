@extends('emails.layout')

@section('title')
[{{ config('app.name') }}] Retour utilisateur — {{ $typeLabel ?? 'Autre' }}
@endsection

@section('content')
<p><strong>Nouveau retour utilisateur</strong></p>

<p><strong>Type :</strong> {{ $typeLabel ?? 'Autre' }}</p>

@if(!empty($pseudo))
<p><strong>Pseudo :</strong> {{ $pseudo }}</p>
@endif

@if(!empty($url))
<p><strong>URL :</strong> <a href="{{ $url }}">{{ $url }}</a></p>
@endif

<p><strong>Message :</strong></p>
<p>{{ $feedbackMessage ?? '-' }}</p>

@if(!empty($hasAttachment) && !empty($attachmentName))
<p><em>Pièce jointe : {{ $attachmentName }}</em></p>
@endif
@endsection
