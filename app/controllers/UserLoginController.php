<?php

class UserLoginController extends \BaseController
{

	public function show()
	{
		return View::make('users.authenticate', ['mode' => 'login']);
	}

	public function logout()
	{

	}

}
