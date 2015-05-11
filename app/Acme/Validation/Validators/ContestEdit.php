<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class ContestEdit extends V
{
	public $rules = [

		'contest_region_id' => 'sometimes|required|integer|exists:contest_regions,id|unique_with:contests,year,season',
		'title'             => 'sometimes|required|max:255',
		'year'              => 'sometimes|required|integer|digits:4',
		'season'            => 'sometimes|required|in:F,S',
		'is_active'         => 'sometimes|required|in:0,1',
	];
}
