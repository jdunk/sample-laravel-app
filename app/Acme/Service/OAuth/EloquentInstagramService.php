<?php namespace Acme\Service\OAuth;

use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Database\QueryException;

use \DB;

use League\OAuth2\Client\Provider\Instagram;

use Acme\Service\User\Registration;
use Acme\Service\User\ProfileImage;
use Acme\Model\Eloquent\InstagramAccount;
use Acme\Validation\Exception as ValidationException;
use Acme\Validation\Validators\InstagramOAuthRegistration;

/*
 * @note OAuth2\Client\Entity\User properties available for Instagram:
 * [
		'uid' => $response->data->id,
		'nickname' => $response->data->username,
		'name' => $response->data->full_name,
		'description' => $description,
		'imageUrl' => $response->data->profile_picture,
	]
 */

/**
 * Class EloquentInstagramService
 * @package Acme\Service\OAuth
 * @see https://github.com/thephpleague/oauth2-client
 */
class EloquentInstagramService implements InstagramService, ProviderService
{
	/**
	 * @var Store
	 */
	protected $session;

	/**
	 * @var Instagram oauth2 client
	 */
	protected $instagram;

	/**
	 * @var Registration
	 */
	protected $userRegistrationService;

	/**
	 * @var ProfileImage for sideloaded IG profile photo
	 */
	protected $profileImage;

	/**
	 * @var int Runaway control
	 */
	protected $registrationTryCount = 0;

	/**
	 * @var InstagramAccount
	 */
	protected $instagramAccount;

	public function __construct(
		Store $session,
		Instagram $instagram,
		Registration $userRegistrationService,
		InstagramAccount $instagramAccount,
		ProfileImage $profileImage
	)
	{
		$this->instagram = $instagram;
		$this->session = $session;
		$this->userRegistrationService = $userRegistrationService;
		$this->instagramAccount = $instagramAccount;
		$this->profileImage = $profileImage;

		$this->userRegistrationService->setRegistrationValidator(
			new InstagramOAuthRegistration());
	}

	public function handleOauthReturn(Request $request, $aroUserId)
	{
		$code = $request->get('code');
		if (empty($code))
			throw new OAuthException('There was no code provided.');

		$token = $this->instagram->getAccessToken('authorization_code', [
			'code' => $code
		]);

		if (empty($token))
			throw new OAuthException('Did not receive valid token.');

		try {
			$userDetails = $this->instagram->getUserDetails($token);
		} catch (\Exception $e) {
			throw new OAuthException('Instagram said ' . $e->getMessage());
		}

		$igAccount = $this->getInstagramAccount($userDetails->uid);

		if ($igAccount) {
			if ($aroUserId && $igAccount->user_id != $aroUserId) {
				throw new OAuthException('This instagram account belongs to another user.');
			}

			$this->updateAccessToken($igAccount, $token);
			return array_except($igAccount->user->toArray(), ['password']);
		}

		try {
			$providerUser = new ProviderUser($userDetails->getArrayCopy());

			return $this->registerOAuthUser($providerUser);
		} catch (\Exception $e) {
			throw new OAuthException('Instagram said ' . $e->getMessage());
		}

	}

	public function getRedirectUrl()
	{
		$authUrl = $this->instagram->getAuthorizationUrl();
		$this->session->put('oauth2state', $this->instagram->state);
		return $authUrl;
	}


	public function registerOAuthUser(ProviderUser $providerUser)
	{
		$suggestedUsername = $this->userRegistrationService->suggestUsername(
			$providerUser->getLocalUsernameSeed());

		DB::beginTransaction();

		try {
			$user = $this->userRegistrationService->process([
				'username' => $suggestedUsername,
				'name' => $suggestedUsername,
				'email' => null,
				'password' => substr(md5(rand()), 0, 7)
			]);

			$this->instagramAccount->newInstance([
				'user_id' => $user['id'],
				'nickname' => $providerUser->get('nickname'),
				'name' => $providerUser->get('name'),
				'description' => $providerUser->get('description'),
				'image_url' => $providerUser->get('imageUrl')
			])->save();

			if ($providerUser->get('imageUrl'))
				$this->profileImage->sideloadForUser($user->id, $providerUser->get('imageUrl'));

			DB::commit();

			return array_except($user, ['password']);
		} catch (ValidationException $ve) {
			DB::rollback();

			throw new OAuthException('Failed to create user account.');
		} catch (QueryException $qe) {
			DB::rollback();

			// probably suggested username race?
			if ($this->registrationTryCount > 5)
				throw new OAuthException('Failed to create user account after five attempts.');

			$this->registrationTryCount++;
			return $this->registerOAuthUser($providerUser);
		}


	}

	public function getInstagramAccount($uid)
	{
		$qb = $this->instagramAccount->newQuery();

		$igAccount = $qb->where('uid', $uid)->with('user')->first();

		if (empty($igAccount))
			return null;

		return $igAccount;
	}

	public function updateAccessToken(InstagramAccount $igAccount, $token)
	{
		$igAccount->access_token = $token;
		return $igAccount->save();
	}
} 
