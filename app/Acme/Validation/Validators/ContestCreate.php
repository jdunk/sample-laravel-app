<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class ContestCreate extends V
{
	public $rules = [

		'user_id'           => 'required|integer|exists:users,id',
		'contest_region_id' => 'required|integer|exists:contest_regions,id|unique_with:contests,year,season',
		'title'             => 'required|max:255',
		'year'              => 'required|integer|digits:4',
		'season'            => 'required|in:F,S'
	];
}
