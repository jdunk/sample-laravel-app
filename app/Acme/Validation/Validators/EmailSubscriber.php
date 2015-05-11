<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class EmailSubscriber extends V
{
	public $rules = [
		'email' => 'required|email',
	];
}
