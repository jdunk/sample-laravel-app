<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class FollowUpdate extends V
{
	public $rules = [

		'is_hushed' => 'required|in:0,1',
	];
}
