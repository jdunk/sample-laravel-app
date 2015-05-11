<?php namespace Acme\Service;

abstract class Base
{
	protected $errors;

	public function addError($message, $field = null)
	{
		if (!$this->errors)
			$this->errors = new MessageBag();

		if (!$field)
			$field = 'error';

		$this->errors->add($field, $message);

		return $this->errors;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function setErrors(MessageBag $errors)
	{
		$this->errors = $errors;
	}
} 
