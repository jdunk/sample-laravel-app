<?php

use Acme\Service\User\ProfileImage;

class MeController extends \BaseController
{

	public function __construct(
		ProfileImage $profileImageService
	)
	{
		$this->profileImageService = $profileImageService;
	}

	/**
	 * Display currently logged in user.
	 * GET /me
	 *
	 * @return Response
	 */
	public function show()
	{
		return Response::json(array_only(Auth::user()->toArray(), ['id', 'username', 'name', 'email', 'image_small', 'image_large']));
	}

	/**
	 * Set profile image for authenticated user.
	 * POST /me/image
	 *
	 * @return Response
	 */
	public function setImage()
	{

		$image = Image::make(Input::get('image'));
		if (!$image)
			throw new \Acme\ClientException('We had a problem with the image you uploaded.
			Please try another image.');

		try {
			$result = $this->profileImageService->setForUser(Auth::user()->id(), $image, []);
			return Response::json($result, 201);
		} catch (\Exception $e) {
			throw new \Acme\ClientException($e->getMessage());
		}

	}

	/**
	 * Profile editor html home.
	 * GET /me/edit
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update my profile
	 * PUT /me
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function update()
	{
		//
	}

	/**
	 * Deactivate my account
	 * DELETE /me
	 *
	 * @param  int $id
	 * @return Response
	 */
	public function destroy()
	{
		//
	}

}
