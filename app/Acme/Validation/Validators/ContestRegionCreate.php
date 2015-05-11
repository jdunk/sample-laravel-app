<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class ContestRegionCreate extends V
{
	public $rules = [

		'title'      => 'required|max:255',
		'short_code' => 'required|max:255|alpha_dash|unique:contest_regions',
	];
}
