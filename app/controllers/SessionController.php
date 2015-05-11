<?php

use \Acme\Service\User\Session;
use \Acme\Validation\Exception as ValidationException;
use \Acme\Service\User\LoginException;

class SessionController extends \BaseController {

	protected $sessionService;

	public function __construct(Session $sessionService)
	{
		$this->sessionService = $sessionService;
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /sessions
	 *
	 * @return Response
	 */
	public function store()
	{
		try
		{
			$user = $this->sessionService->create(Input::all());
		}
		catch (LoginException $e)
		{
			if (Request::ajax())
				return Response::json(array('message' => 'Invalid login.'), 400);

			return Response::make('Invalid login.', 400);
		}

		if (Request::ajax())
			return Response::json(array_only($user->toArray(), ['id', 'username', 'name', 'email', 'image_small', 'image_large']), 201);

		return Redirect::intended('/');
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /sessions/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
}
