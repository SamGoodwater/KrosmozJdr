@extends('emails.layout')

@section('title', $subject ?? config('app.name'))

@section('content')
<p>{{ $greeting ?? 'Bonjour !' }}</p>

@foreach($lines ?? [] as $line)
<p>{{ $line }}</p>
@endforeach

@if(!empty($actionUrl) && !empty($actionText))
<p style="margin: 1.5rem 0;">
    <a href="{{ $actionUrl }}" class="btn">{{ $actionText }}</a>
</p>
@endif

@if(!empty($footer))
<p>{{ $footer }}</p>
@endif
@endsection
