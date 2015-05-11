@extends('layouts.master')

@section('meta')

@stop

@section('styles')

@stop

@section('footer-scripts')

@stop

@section('heading1')
   	Oops.  We've encountered a problem.
@stop

@section('content')
	<div class="alert alert-warning" role="alert">{{ $message }}</div>
@stop
