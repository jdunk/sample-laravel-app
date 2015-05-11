<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class CommentCreate extends V
{
	public $rules = [

		'text'             => 'required',
		'commentable_type' => 'required',
		'commentable_id'   => 'required'
	];
}
