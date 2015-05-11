<?php namespace Acme\Service\String\Decorators;

use Acme\Service\String\StringDecorator;

class NewUserEmail extends StringDecorator
{
	public function defaultContent() {
		return 'Welcome to ...';
	}
} 
