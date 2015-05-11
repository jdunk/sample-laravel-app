<?php
use Acme\Service\OAuth\ProviderUser;

class OAuthServiceTest extends TestCase {

	/**
	 * @covers Acme\Service\OAuth\ProviderUser::getLocalUsernameSeed
	 */
	public function testGetLocalUsernameSeed()
	{
		$providerUser = new ProviderUser([
			'uid' => '98426734',
			'nickname' => 'harry',
			'name' => 'Harry Smith',
			'firstName' => 'Harry',
			'lastName' => 'Smith'
		]);

		$seed = $providerUser->getLocalUsernameSeed();
		$this->assertEquals('harry', $seed);

		$providerUser = new ProviderUser([
			'uid' => '98426734',
			'nickname' => 'sarah22',
			'name' => 'Sarah Smith',
			'firstName' => 'Sarah',
			'lastName' => 'Smith'
		]);

		$seed = $providerUser->getLocalUsernameSeed();
		$this->assertEquals('sarah22', $seed);
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
