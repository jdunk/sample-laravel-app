<?php

use \Acme\Service\User\Registration;

class UserRegistrationController extends \BaseController {

	protected $userRegistration;

	public function __construct(Registration $registration)
	{
		$this->registration = $registration;
	}

	/**
	 * Traditional HTML view as home to authenticate app in register mode
	 * @return mixed
	 */
	public function show()
	{
		return View::make('users.authenticate', ['mode' => 'register']);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /user
	 *
	 * @return Response
	 */
	public function store()
	{
		$i = Input::all();

		// defaults to username for now
		$i['name'] = Input::get('username');

		$user_array = $this->registration->process($i);

		return Response::json(array_only($user_array, ['id', 'username', 'name', 'email']));
	}

	public function checkUsernameAvailability($username) {
		$available = $this->registration->usernameAvailable($username);
		return Response::json(['available' => $available], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /user/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
