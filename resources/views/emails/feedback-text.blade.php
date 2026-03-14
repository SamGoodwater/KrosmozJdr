Nouveau retour utilisateur

Type : {{ $typeLabel ?? 'Autre' }}

@if(!empty($pseudo))
Pseudo : {{ $pseudo }}

@endif
@if(!empty($url))
URL : {{ $url }}

@endif
Message :
{{ $feedbackMessage ?? '-' }}

@if(!empty($hasAttachment) && !empty($attachmentName))
Pièce jointe : {{ $attachmentName }}
@endif
