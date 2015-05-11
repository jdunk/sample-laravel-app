<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class ContestRegionEdit extends V
{
	public $rules = [

		'title'      => 'sometimes|required|max:255',
		'short_code' => 'sometimes|required|max:255|alpha_dash|unique:contest_regions'
	];
}
