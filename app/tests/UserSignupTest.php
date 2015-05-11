<?php

use Acme\Validation\Exception as ValidationException;
use Acme\Model\Eloquent\User;

class UserSignupTest extends TestCase
{
	public function createTestUser()
	{
		$response = $this->action(
			'POST',
			'UserRegistrationController@store',
			[
				'username' => 'testuser',
				'password' => 'totallysecure123',
				'name' => 'Test User',
				'email' => 'testuser@test.com',
			]
		);
	}

	public function testUserSignup()
	{
		try
		{
			$this->createTestUser();

			$this->assertTrue(true);
		}
		catch (Exception $e)
		{
			$this->fail("Unable to create user: " . $e->getMessage());
		}
	}

	public function testUsernameTaken()
	{
		$this->createTestUser();

		try
		{
			$response = $this->action(
				'POST',
				'UserRegistrationController@store',
				[
					'username' => 'testuser',
					'password' => 'totallysecure123',
					'name' => 'Test User',
					'email' => 'testuser2@test.com',
				]
			);
		}
		catch (ValidationException $e)
		{
			$this->assertTrue(true);
			return true;
		}
		catch (Exception $e)
		{
			$this->fail('Non-validation exception thrown during attempted creation of user with already-taken username: ' . $e->getMessage());
		}

		$this->fail('User created with already-taken username.');
	}

	public function testEmailTaken()
	{
		$this->createTestUser();

		try
		{
			$response = $this->action(
				'POST',
				'UserRegistrationController@store',
				[
					'username' => 'testuser2',
					'password' => 'totallysecure123',
					'name' => 'Test User',
					'email' => 'testuser@test.com',
				]
			);
		}
		catch (ValidationException $e)
		{
			$this->assertTrue(true);
			return true;
		}
		catch (Exception $e)
		{
			$this->fail('Non-validation exception thrown during attempted creation of user with already-taken email address: ' . $e->getMessage());
		}

		$this->fail('User created with already-taken email address.');
	}

	/**
	 * @covers Acme\Service\User\Registration::suggestUsername
	 */
	public function testUsernameSuggestion() {
		User::create([
			'username' => 'walterwhite',
			'email' => 'walter@example.com',
			'password' => '12343829',
			'name' => 'Walter White'
		]);

		$RegistrationSvc = App::build('Acme\Service\User\EloquentRegistration');
		$result = $RegistrationSvc->suggestUsername('walterwhite');
		$this->assertNotEmpty($result);
		$this->assertNotEquals($result, 'walterwhite');
	}

	/**
	 * @covers Acme\Service\User\Registration::usernameAvailable
	 */
	public function testUsernameAvailability() {
		User::create([
			'username' => 'walterwhite',
			'email' => 'walter@example.com',
			'password' => '12343829',
			'name' => 'Walter White'
		]);

		$RegistrationSvc = App::build('Acme\Service\User\EloquentRegistration');

		$result = $RegistrationSvc->usernameAvailable('walterwhite');
		$this->assertFalse($result);

		$result = $RegistrationSvc->usernameAvailable('jesse');
		$this->assertTrue($result);
	}
}
