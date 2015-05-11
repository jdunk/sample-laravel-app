<?php

use \Acme\Service\Follow\FollowService;

class FollowController extends \BaseController {

	protected $followService;

	public function __construct(FollowService $followService)
	{
		$this->followService = $followService;
	}

	/**
	 * Display a listing of what a user is following.
	 * GET /me/follows
	 * -- or --
	 * GET /users/{id}/follows
	 *
	 * @return Response
	 */
	public function index($user_id=null)
	{
		// if $user_id is not set, then /me/follows must have been used
		if (is_null($user_id))
			$user_id = Auth::user()->id;

		$hushed_only = (bool) Input::get('is_hushed');

		if ($hushed_only && $user_id != Auth::user()->id)
			ARO::must('follow.read_hushed');

		return Response::json($this->followService->getFollows($user_id, $hushed_only));
	}

	/**
	 * Display a listing of users following a given followable.
	 * GET /me/followers
	 * -- or --
	 * GET /users/{id}/followers
	 * -- or --
	 * GET /designers/{id}/followers
	 *
	 * @return Response
	 */
	public function followersIndex($id=null)
	{
		// if $id is not set, then /me/followers must have been used
		if (is_null($id))
			$id = Auth::user()->id;

		$followable_type = 'User'; // default

		if (Request::segment(1) == 'designers')
			$followable_type = 'Designer';

		return Response::json($this->followService->getFollowers($followable_type, $id));
	}

	/**
	 * follow CREATE
	 * POST /me/follows
	 * -- or --
	 * POST /users/{user.id}/follows
	 *
	 * @return Response
	 */
	public function store(...$args)
	{
		if (count($args))
		{
			$user_id = $args[0];

			if ($user_id != Auth::user()->id)
				ARO::must('follow.create');
		}
		else
		{
			$user_id = \Auth::user()->id;
		}

		$data = Input::all();
		$data['user_id'] = $user_id;
		$follow_data = $this->followService->createFollow($data);

		return Response::json($follow_data, 201);
	}

	/**
	 * follow UPDATE
	 * PUT /me/follows/{id}
	 * -- or --
	 * PUT /users/{user.id}/follows/{id}
	 *
	 * @param  int  $id or [$user_id, $id]
	 * @return Response
	 */
	public function update(...$args)
	{
		if (count($args) > 1)
			array_shift($args);

		$id = $args[0];

		ARO::must('follow.update', $this->followService->getOwnerRlacFn($id, Auth::user()->id));

		$follow_data = $this->followService->updateFollow($id, Input::all());

		return Response::json($follow_data, 200);
	}

	/**
	 * follow DELETE
	 * DELETE /me/follows/{id}
	 * -- or --
	 * DELETE /users/{user.id}/follows/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(...$args)
	{
		if (count($args) > 1)
			array_shift($args);

		$id = $args[0];

		ARO::must('follow.delete', $this->followService->getOwnerRlacFn($id, Auth::user()->id));

		$this->followService->destroyFollow($id);
		return Response::make('No Content', 204);
	}
}
