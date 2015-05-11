<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class FlaggedItemUpdate extends V
{
	public $rules = [

		'severity' => 'sometimes|required|integer|min:1|max:10',
		'action' => 'sometimes|required|max:255',
		'action_user_id' => 'sometimes|required|exists:users,id',
	];
}
