<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class FollowCreate extends V
{
	public $rules = [

		'user_id'         => 'required|integer|unique_with:follows,followable_type,followable_id',
		'followable_type' => 'required|in:User,Designer',
		'followable_id'   => 'required|integer'
	];

	protected $customMessages = [
		'unique_with' => 'Already following.'
	];
}
