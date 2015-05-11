<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'debug' => true,

	/**
	 * Note usage of append_config(), kind of wonky but seems to be required.
	 */
	'providers' => append_config(array(
		'Way\Generators\GeneratorsServiceProvider',
		'Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider'
		// other laravel stuff we use for local/dev only
	)),

);
