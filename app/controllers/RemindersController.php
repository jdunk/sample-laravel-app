<?php

class RemindersController extends \BaseController {

	/**
	 * Display the password reminder view.
	 *
	 * @return Response
	 */
	public function getRemind()
	{
		return View::make('users.authenticate', ['mode' => 'forgot']);
	}

	/**
	 * Handle a POST request to remind a user of their password.
	 *
	 * @return Response
	 */
	public function postRemind()
	{
		$response = Password::remind(Input::only('email'), function($message) {
			$message->from(Config::get('acme.systemEmail'), Config::get('acme.systemFrom'));
			$message->subject('Password Reset Instructions');
		});

		switch ($response)
		{
			case Password::INVALID_USER:
				if (Request::ajax()) {
					return Response::json(['message' => Lang::get($response)], 400);
				} else {
					return Redirect::back()->with('error', Lang::get($response));
				}

			case Password::REMINDER_SENT:
				if (Request::ajax()) {
					/*
					 * ???
					 * $oldResponse = 'A password reset link has been emailed to '
						. Input::get('email')];
					 */

					return Response::json(['message' =>  Lang::get($response)], 200);
				} else {
					return Redirect::back()->with('status', Lang::get($response));
				}

		}
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function getReset($token = null)
	{
		if (is_null($token)) App::abort(404);

		return View::make('users.reset')->with('token', $token);
	}

	/**
	 * Handle a POST request to reset a user's password.
	 *
	 * @return Response
	 */
	public function postReset()
	{
		$credentials = Input::only(
			'email', 'password', 'password_confirmation', 'token'
		);

		Password::validator(function($credentials)
		{
			$v = User::newPasswordValidator($credentials);
			return $v->passes();
		});

		$response = Password::reset($credentials, function($user, $password)
		{
			$user->password = Hash::make($password);

			$user->save();
		});

		switch ($response)
		{
			case Password::INVALID_PASSWORD:
			case Password::INVALID_TOKEN:
			case Password::INVALID_USER:
				Session::flash('warning', Lang::get($response));
				return Redirect::back()->with('error', Lang::get($response));

			case Password::PASSWORD_RESET:
				Session::flash('info', Lang::get('reminders.success'));
				return Redirect::action( 'UserLoginController@show' );
		}
	}

}
