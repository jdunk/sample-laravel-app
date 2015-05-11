<?php

use Acme\Model\Eloquent\Sand;
use Acme\Storage\User\UserRoleRepository;

class HomeController extends BaseController {

	public function __construct(UserRoleRepository $userRoleRepository) 
	{
		$this->userRoleRepository = $userRoleRepository;
	}

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
		
		return View::make('hello');
	}

}
