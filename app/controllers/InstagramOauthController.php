<?php

use \Acme\Service\OAuth\InstagramService;
use \Acme\Service\OAuth\OAuthException;
use \Acme\ClientException;

class InstagramOauthController extends \BaseController
{

	/**
	 * @var \Acme\Service\OAuth\InstagramService
	 */
	protected $instagramService;

	public function __construct(InstagramService $instagramService)
	{
		$this->instagramService = $instagramService;
	}

	public function oauthReturnHandler()
	{
		try {
			$user = $this->instagramService->handleOauthReturn(Request::instance(), Auth::id());

			if (empty($user['email'])) {

			}

			Auth::login(User::find($user['id']));

			return Redirect::to('/');
		} catch(OAuthException $oae) {
			throw new ClientException('Instagram had a problem: ' . $oae->getMessage());
		}
	}

	public function oauthRedirect()
	{
		return Redirect::to($this->instagramService->getRedirectUrl());
	}

}
