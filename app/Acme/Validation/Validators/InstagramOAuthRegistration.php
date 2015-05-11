<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class InstagramOAuthRegistration extends V
{
	public $rules = [

		'username' => 'required|between:2,32|alpha_num_underscore|no_consecutive_underscores|unique:users',
		'password' => 'required|between:7,20',
		'name'     => 'required|between:2,50',
		'email'    => 'email|unique:users',
	];
}
