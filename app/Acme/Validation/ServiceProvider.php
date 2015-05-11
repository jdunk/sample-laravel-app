<?php namespace Acme\Validation;

use Illuminate\Support\ServiceProvider as CoreServiceProvider;

class ServiceProvider extends CoreServiceProvider {

	public function boot()
	{
		\Validator::extend('alpha_num_underscore', function($attr, $val, $params)
		{
			return preg_match('/^[\pL\pM\pN_]+$/u', $val);
		});

		\Validator::extend('no_consecutive_underscores', function($attr, $val, $params)
		{
			return ! preg_match('/__/', $val);
		});
	}

	public function register() {}
}
