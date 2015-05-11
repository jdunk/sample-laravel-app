<?php

namespace Acme\Validation;

abstract class Validator
{
	public $rules = [];
	protected $errors;
	protected $customMessages = [
		'unique_with' => 'This combination of :fields is unavailable.',
	];

	public function validate(array $attrs)
	{
		$v = \Validator::make($attrs, $this->rules, $this->customMessages ?: []);

		if ($v->fails())
		{
			$this->errors = $v->messages();
			throw new Exception('Validation failed', $this->getErrors());
		}
	}

	public function getErrors()
	{
		return $this->errors;
	}
}
