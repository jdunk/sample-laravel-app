@extends('emails.layout')

@section('content')

<h2>Welcome to {{ Config::get('acme.title') }}!</h2>

<p style="margin-bottom: 20px;">
	We're going to be doing some amazing things together!  Stay tuned and follow your favorite Designers at
	<a href="https://acme.com/">Acme.com</a>!
</p>

@stop

