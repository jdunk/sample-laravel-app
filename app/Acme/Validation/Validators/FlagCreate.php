<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class FlagCreate extends V
{
	public $rules = [

		'user_id'        => 'required|integer|exists:users,id|unique_with:flags,flaggable_type,flaggable_id',
		'flaggable_type' => 'required|in:Comment,Post',
		'flaggable_id'   => 'required|integer'
	];

	protected $customMessages = [
		'unique_with' => 'Already flagged.'
	];
}
