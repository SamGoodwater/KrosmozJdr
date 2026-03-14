{{ $greeting ?? 'Bonjour !' }}

@foreach($lines ?? [] as $line)
{{ $line }}

@endforeach
@if(!empty($actionUrl) && !empty($actionText))
{{ $actionText }} : {!! $actionUrl !!}

@endif
@if(!empty($footer))
{{ $footer }}
@endif
