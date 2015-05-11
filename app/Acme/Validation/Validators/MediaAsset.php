<?php namespace Acme\Validation\Validators;

use Acme\Validation\Validator as V;

class MediaAsset extends V
{
	public $rules = [
		'media_assetable_type' => 'required|in:Designer,Judge,JudgeContestant,Product,Contest,Post,User',
		'media_assetable_id' => 'required|numeric',
		'user_id' => 'required|exists:users,id',
		'type' => 'required|in:Image,YouTube',
		'title' => 'required',
		'display_order' => 'integer',
		'lat' => 'numeric',
		'lng' => 'numeric'
	];
}
