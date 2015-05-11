<?php namespace Acme\Service\String;


abstract class StringDecorator
{
	abstract public function defaultContent();

	/**
	 * Add any functions in concrete implementation to the template scope?
	 */
	public function getDecorated() {
		return $this->defaultContent();
	}

} 
