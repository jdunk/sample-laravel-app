@extends('layouts.master')

@section('heading1')
	Reset Password
@stop

@section('content')

	{{ Former::vertical_open()
	  ->id('ResetPasswordForm')
	  //->secure()
	  ->controller('RemindersController@postReset')
	  //->rules(['email' => 'required', 'password' => 'required'])
	  ->method('POST') }}

	<input type="hidden" name="token" value="{{ $token }}">

	{{ Former::input_lg_email('email')
	    ->required()
	    ->placeholder('Email Address') }}

	{{ Former::input_lg_password('password')
	    ->required()
	    ->placeholder('New Password') }}

	    {{ Former::input_lg_password('password_confirmation')
	    ->required()
	    ->placeholder('Confirm New Password') }}

	{{ Former::actions()
	    ->large_primary_submit('Set New Password') }}

	{{ Former::close() }}

@stop
