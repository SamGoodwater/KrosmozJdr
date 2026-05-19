Copie de ton retour — {{ $typeLabel ?? 'Autre' }}

Type : {{ $typeLabel ?? 'Autre' }}
@if(!empty($url))
URL : {{ $url }}
@endif

Message :
{{ $feedbackMessage ?? '-' }}

Tu as demandé à recevoir ce récapitulatif par email.
