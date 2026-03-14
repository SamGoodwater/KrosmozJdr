Bonjour {{ $userName }},

Merci pour ton inscription sur {{ config('app.name') }}. Clique sur le lien ci-dessous pour vérifier ton adresse email :

{!! $verificationUrl !!}

Ce lien expire dans 60 minutes. Si tu n'as pas créé de compte, tu peux ignorer cet email.
