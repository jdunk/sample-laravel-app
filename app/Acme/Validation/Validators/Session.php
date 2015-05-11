<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class Session extends V
{
	public $rules = [

		'login'       => 'required',
		'password'    => 'required|between:7,20',
		'remember_me' => 'in:0,1',
	];
}
