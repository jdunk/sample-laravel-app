<?php
use \Mockery as M;

use Acme\Service\OAuth\EloquentInstagramService;
use Acme\Model\Eloquent\InstagramAccount;
use Acme\Model\Eloquent\User;

class InstagramOAuthTest extends TestCase
{
	/**
	 * @covers Acme\Service\OAuth\EloquentInstagramService::getInstagramAccount
	 */
	public function testGetInstagramAccount()
	{
		$Session = M::mock('Illuminate\Session\Store');
		$Instagram = M::mock('League\OAuth2\Client\Provider\Instagram');
		$RegistrationSvc = App::build('Acme\Service\User\EloquentRegistration');
		$InstagramAccount = new InstagramAccount();
		$ProfileImage = M::mock('Acme\Service\User\EloquentS3ProfileImage');

		$eis = new EloquentInstagramService(
			$Session,
			$Instagram,
			$RegistrationSvc,
			$InstagramAccount,
			$ProfileImage);

		User::create([
			'username' => 'sarabina',
			'email' => 'sarabina@gmail.com',
			'password' => '123123123123',
			'name' => 'Sara Bina Bina'
		]);

		$user = User::create([
			'username' => 'saragina',
			'email' => 'saragina@gmail.com',
			'password' => '123123123123',
			'name' => 'Sara Fina Gina'
		]);

		InstagramAccount::create([
			'uid' => '154323',
			'user_id' => $user->id,
			'image_url' => 'http://someimage.com/someimage.jpg',
			'nickname' => 'sarafina',
			'name' => 'Sara Balls',
			'access_token' => 'n873nriay987wydi87h3y'
		]);

		$result = $eis->getInstagramAccount('154323');

		$this->assertArrayHasKey('user', $result);
		$this->assertEquals($result['user']['id'], $user->id);
		$this->assertEquals($result['uid'], '154323');
		$this->assertEquals($result['access_token'], 'n873nriay987wydi87h3y');
	}

	/**
	 * @covers Acme\Service\OAuth\EloquentInstagramService::updateAccessToken
	 */
	public function testUpdateAccessToken() {
		$Session = M::mock('Illuminate\Session\Store');
		$Instagram = M::mock('League\OAuth2\Client\Provider\Instagram');
		$RegistrationSvc = App::build('Acme\Service\User\EloquentRegistration');
		$InstagramAccount = new InstagramAccount();
		$ProfileImage = M::mock('Acme\Service\User\EloquentS3ProfileImage');

		$eis = new EloquentInstagramService(
			$Session,
			$Instagram,
			$RegistrationSvc,
			$InstagramAccount,
			$ProfileImage);

		$user = User::create([
			'username' => 'saragina',
			'email' => 'saragina@gmail.com',
			'password' => '123123123123',
			'name' => 'Sara Fina Gina'
		]);

		$igAccount = InstagramAccount::create([
			'uid' => '154323',
			'user_id' => $user->id,
			'image_url' => 'http://someimage.com/someimage.jpg',
			'nickname' => 'sarafina',
			'name' => 'Sara Balls',
			'access_token' => 'n873nriay987wydi87h3y'
		]);

		$eis->updateAccessToken($igAccount, 'iAmAnUpdatedAccessToken');

		$result = InstagramAccount::find($igAccount->id);
		$this->assertEquals($result->access_token, 'iAmAnUpdatedAccessToken');
	}

	/**
	 * @covers Acme\Service\OAuth\EloquentInstagramService::updateAccessToken
	 */
	public function testRegisterOAuthUser() {
		$Session = M::mock('Illuminate\Session\Store');
		$Instagram = M::mock('League\OAuth2\Client\Provider\Instagram');
		$RegistrationSvc = App::build('Acme\Service\User\EloquentRegistration');
		$InstagramAccount = new InstagramAccount();
		$ProfileImage = M::mock('Acme\Service\User\EloquentS3ProfileImage');

		// set a conflicting username.
		User::create([
			'username' => 'harry',
			'email' => 'harry@gmail.com',
			'password' => '123123123123',
			'name' => 'Big Harry'
		]);

		$eis = new EloquentInstagramService(
			$Session,
			$Instagram,
			$RegistrationSvc,
			$InstagramAccount,
			$ProfileImage);

		$result = $eis->registerOAuthUser(new Acme\Service\OAuth\ProviderUser([
			'uid' => '123445',
			'nickname' => 'harry',
			'name' => 'Harry Gump'
		]));

		$this->assertNotEquals($result['username'], 'harry');
		$this->assertArrayNotHasKey('password', $result);
	}

	public function testRegisterWeirdNicknameOAuthUser() {
		$Session = M::mock('Illuminate\Session\Store');
		$Instagram = M::mock('League\OAuth2\Client\Provider\Instagram');
		$RegistrationSvc = App::build('Acme\Service\User\EloquentRegistration');
		$InstagramAccount = new InstagramAccount();
		$ProfileImage = M::mock('Acme\Service\User\EloquentS3ProfileImage');

		User::create([
			'username' => 'user',
			'email' => 'user@gmail.com',
			'password' => '123123123123',
			'name' => 'Super Creative'
		]);

		$eis = new EloquentInstagramService(
			$Session,
			$Instagram,
			$RegistrationSvc,
			$InstagramAccount,
			$ProfileImage);

		$result = $eis->registerOAuthUser(new Acme\Service\OAuth\ProviderUser([
			'uid' => '123445',
			'nickname' => '¥¥¥¥¥______',
			'name' => 'h¥¥¥¥¥¥¥¥'
		]));

		$this->assertArrayHasKey('username', $result);
		$this->assertArrayHasKey('id', $result);
		$this->assertNotEquals($result['username'], 'user');
		$this->assertArrayNotHasKey('password', $result);
	}

	public function setUp()
	{
		parent::setUp();
	}

	public function tearDown()
	{
		parent::tearDown();
	}

}
