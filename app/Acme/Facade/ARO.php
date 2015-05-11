<?php namespace Acme\Facade;

use Illuminate\Support\Facades\Facade;

class ARO extends Facade {

	protected static function getFacadeAccessor() {
		return 'aro';
	}

}
