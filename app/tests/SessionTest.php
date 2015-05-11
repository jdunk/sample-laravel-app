<?php

use Acme\Validation\Exception as ValidationException;
use Acme\Model\Eloquent\User;

class SessionTest extends TestCase
{
	protected $userData = [
		'username' => 'testuser',
		'password' => 'totallysecure123',
		'name' => 'Test User',
		'email' => 'testuser@test.com',
	];

	public function createTestUser()
	{
		$response = $this->action(
			'POST',
			'UserRegistrationController@store',
			$this->userData
		);
	}

	public function testValidLogin()
	{
		$this->createTestUser();

		// Test login with username:
		/*
		$response = $this->action(
			'POST',
			'SessionController@store',
			[
				'login' => $this->userData['username'],
				'password' => $this->userData['password']
			]
		);
		*/

		// test ajax response
		$response = $this->call(
			'POST',
			'/sessions',
			[
				'login' => $this->userData['username'],
				'password' => $this->userData['password']
			],
			[],
			['HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(201);

		// test login with email
		$response = $this->call(
			'POST',
			'/sessions',
			[
				'login' => $this->userData['email'],
				'password' => $this->userData['password']
			],
			[],
			['HTTP_X-Requested-With' => 'XMLHttpRequest']
		);

		$this->assertResponseStatus(201);
	}

	public function testInvalidLogin()
	{
		$this->createTestUser();

		// Test login with username:

		$response = $this->action(
			'POST',
			'SessionController@store',
			[
				'login' => $this->userData['username'] . '_no_existe',
				'password' => $this->userData['password']
			]
		);

		$this->assertResponseStatus(400);

		$response = $this->action(
			'POST',
			'SessionController@store',
			[
				'login' => $this->userData['username'],
				'password' => $this->userData['password'] . '123'
			]
		);

		$this->assertResponseStatus(400);

		$response = $this->action(
			'POST',
			'SessionController@store',
			[
				'login' => $this->userData['email'],
				'password' => $this->userData['password'] . '123'
			]
		);

		$this->assertResponseStatus(400);
	}
}
