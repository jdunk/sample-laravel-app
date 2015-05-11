<?php

use Acme\Model\Eloquent\User;
use Acme\Model\Eloquent\Designer;

class FollowTest extends TestCase
{
	protected $followRepo;
	protected $followService;

	public function setUp()
	{
		parent::setUp();
		$this->followRepo = App::make('Acme\Storage\Follow\FollowRepository');
		$this->followService = App::make('Acme\Service\Follow\FollowService');
	}

	public function testFollowCreateUnathorized()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->call(
				'POST',
				'/users/2/follows',
				[
					'followable_type' => 'User',
					'followable_id' => '2',
				]
			);

			$this->fail('User with no permissions able to create follow for another user: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testFollowCreateForOtherUser()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['follow.create']);

		$response = $this->call(
			'POST',
			'/users/2/follows',
			[
				'followable_type' => 'User',
				'followable_id' => '1',
			]
		);

		$this->assertResponseStatus(201);

		$follow = $this->followRepo->find(1);

		$this->assertTrue(is_array($follow));
		$this->assertArrayHasKey('id', $follow);
		$this->assertArrayHasKey('user_id', $follow);
		$this->assertEquals('1', $follow['id']);
		$this->assertEquals('2', $follow['user_id']);

		$data = $response->getData();
		$this->validateFollowDataStructure($data);
	}

	public function testFollowCreateOwn()
	{
		$this->mockLogIn(1);

		$response = $this->action(
			'POST',
			'FollowController@store',
			[
				'followable_type' => 'User',
				'followable_id' => '2',
			]
		);

		$this->assertResponseStatus(201);

		$follow = $this->followRepo->find(1);

		$this->assertTrue(is_array($follow));
		$this->assertArrayHasKey('id', $follow);
		$this->assertArrayHasKey('user_id', $follow);
		$this->assertEquals('1', $follow['id']);
		$this->assertEquals('1', $follow['user_id']);

		$data = $response->getData();
		$this->validateFollowDataStructure($data);
	}

	public function testFollowableTypeError()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'POST',
				'FollowController@store',
				[
					'followable_type' => 'Image',
					'followable_id' => '1234',
				]
			);

			$this->fail('Expected Validation exception for invalid followable_type; none thrown.');
		}
		catch (Acme\Validation\Exception $e)
		{
			$errors = $e->getErrors()->toArray();

			if (! isset($errors['followable_type']))
				$this->fail('Validation exception was thrown, but expected error for invalid followable_type was not found.');

			$this->assertTrue(true);
		}
	}

	public function testNonExistentFollowableError()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'POST',
				'FollowController@store',
				[
					'followable_type' => 'User',
					'followable_id' => '1234',
				]
			);

			$this->fail('Expected Validation exception for invalid [non-existent] followable; none thrown.');
		}
		catch (Acme\Validation\Exception $e)
		{
			$errors = $e->getErrors()->toArray();

			if (! isset($errors['followable_id']))
				$this->fail('Validation exception was thrown, but expected error for invalid followable_id was not found.');

			$this->assertTrue(true);
		}
	}

	public function testUserMayNotFollowSelf()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'POST',
				'FollowController@store',
				[
					'followable_type' => 'User',
					'followable_id' => '1',
				]
			);

			$this->fail('Expected Validation exception for invalid [self-follow] followable; none thrown.');
		}
		catch (Acme\Validation\Exception $e)
		{
			$errors = $e->getErrors()->toArray();

			if (! isset($errors['user_id']))
				$this->fail('Validation exception was thrown, but expected error on user_id was not found.');

			$this->assertTrue(true);
		}
	}

	public function testDuplicateFollowError()
	{
		$this->mockLogIn(1);

		$response = $this->action(
			'POST',
			'FollowController@store',
			[
				'followable_type' => 'User',
				'followable_id' => '2',
			]
		);

		try
		{
			$response = $this->action(
				'POST',
				'FollowController@store',
				[
					'followable_type' => 'User',
					'followable_id' => '2',
				]
			);

			$this->fail('Expected Validation exception for invalid [duplicate] followable; none thrown.');
		}
		catch (Acme\Validation\Exception $e)
		{
			$errors = $e->getErrors()->toArray();

			if (! isset($errors['user_id']))
				$this->fail('Validation exception was thrown, but expected error on user_id was not found.');

			$this->assertTrue(true);
		}
	}

	public function testFollowUpdateUnauthorized()
	{
		$this->mockLogIn(1);

		$this->followService->createFollow([
			'user_id' => '2',
			'followable_type' => 'User',
			'followable_id'   => '1',
		]);

		try
		{
			$response = $this->action(
				'PUT',
				'FollowController@update',
				['id' => '1', 'is_hushed' => '1']
			);

			$this->fail('User with no permissions able to edit another user\'s follow: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testFollowUpdateNonExistent()
	{
		// Also tests that 'follow.update' perm is checked.
		// If is wasn't, this would throw a 403 instead of 404.
		$this->mockLogIn(1);
		Config::set('acl.tester', ['follow.update']);

		try
		{
			$response = $this->action(
				'PUT',
				'FollowController@update',
				[
					'follows'   => '981723941999',
					'is_hushed' => '1',
				]
			);

			$this->fail('Follow update: expected ModelNotFoundException; none thrown');
		}
		catch (Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testFollowUpdateOwn()
	{
		$this->mockLogIn(1);

		$this->followService->createFollow([
			'user_id' => '1',
			'followable_type' => 'User',
			'followable_id'   => '2',
		]);

		$valid_edits = [
			['follows' => '1', 'is_hushed' => '1'],
			['follows' => '1', 'is_hushed' => '0'],
		];

		foreach ($valid_edits as $edit_data)
		{
			$response = $this->action(
				'PUT',
				'FollowController@update',
				$edit_data
			);

			$this->assertResponseStatus(200);

			$res = $this->followRepo->findOrFail(1);

			$this->assertTrue(is_array($res));
			$this->assertArrayHasKey('is_hushed', $res);
			$this->assertEquals($edit_data['is_hushed'], $res['is_hushed']);
		}

		$invalid_edits = [
			[
				'id'        => '1',
				'is_hushed' => '3',
			],
		];

		foreach ($invalid_edits as $edit_data)
		{
			try
			{
				$response = $this->action(
					'PUT',
					'FollowController@update',
					$edit_data
				);

				$this->fail('Follow update validation should have failed, but passed: '
					. json_encode($edit_data));
			}
			catch (Acme\Validation\Exception $e)
			{
				$this->assertTrue(true);
			}
		}
	}

	public function testFollowIndexNonExistent()
	{
		try
		{
			$response = $this->call(
				'GET',
				'/users/9123474741124/follows'
			);

			$this->fail('Follow index for non-existent user: expected ModelNotFoundException; none thrown');
		}
		catch (Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testFollowIndex()
	{
		$this->mockLogIn(1);

		Designer::create([ // Designer #1
			'user_id' => '1',
			'title'   => 'Foo'
		]);
		Designer::create([
			'user_id' => '2',
			'title'   => 'Bar'
		]);
		Designer::create([
			'user_id' => '3',
			'title'   => 'Baz'
		]);
		User::create([ // User #3
			'email' => 'foo',
			'username' => 'foo',
			'name' => 'foo',
			'password' => 'foo',
		]);
		User::create([ // User #4
			'email' => 'foo2',
			'username' => 'foo2',
			'name' => 'foo',
			'password' => 'foo',
		]);
		
		$this->followService->createFollow([
			'user_id' => '1',
			'followable_type' => 'User',
			'followable_id'   => '2',
		]);
		$this->followService->createFollow([
			'user_id' => '1',
			'followable_type' => 'Designer',
			'followable_id'   => '3',
		]);
		$this->followService->createFollow([
			'user_id' => '1',
			'followable_type' => 'User',
			'followable_id'   => '4',
		]);
		$this->followService->createFollow([
			'user_id' => '2',
			'followable_type' => 'Designer',
			'followable_id'   => '2',
		]);
		$this->followService->createFollow([
			'user_id' => '2',
			'followable_type' => 'Designer',
			'followable_id'   => '3',
		]);
		$this->followService->createFollow([
			'user_id' => '2',
			'followable_type' => 'User',
			'followable_id'   => '3',
			'is_hushed'       => '1'
		]);

		$response = $this->call(
			'GET',
			'/me/follows'
		);

		$this->assertResponseStatus(200);
		$this->validatePaginatedFollowsDataStructure($response->getData(), 3);

		// now test that alternate uri syntax does the same
		$response = $this->call(
			'GET',
			'/users/1/follows'
		);

		$this->assertResponseStatus(200);
		$this->validatePaginatedFollowsDataStructure($response->getData(), 3);

		// now test getting another user's follows
		$response = $this->call(
			'GET',
			'/users/2/follows'
		);

		$this->assertResponseStatus(200);
		$this->validatePaginatedFollowsDataStructure($response->getData(), 3);
	}

	public function testFollowIndexHushedUnauthorized()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->call(
				'GET',
				'/users/2/follows',
				['is_hushed' => '1']
			);

			$this->fail('User with no permissions able to view another user\'s hushed follows: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testFollowIndexHushedWithPerm()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['follow.read_hushed']);

		$response = $this->call(
			'GET',
			'/users/2/follows',
			['is_hushed' => '1']
		);

		$this->assertResponseStatus(200);
	}

	public function testFollowIndexHushedOwn()
	{
		$this->mockLogIn(1);

		Designer::create([ // Designer #1
			'user_id' => '1',
			'title'   => 'Foo'
		]);
		Designer::create([
			'user_id' => '2',
			'title'   => 'Bar'
		]);
		Designer::create([
			'user_id' => '3',
			'title'   => 'Baz'
		]);
		Designer::create([
			'user_id' => '4',
			'title'   => 'Biff'
		]);
		User::create([ // User #3
			'email' => 'foo',
			'username' => 'foo',
			'name' => 'foo',
			'password' => 'foo',
		]);
		User::create([ // User #4
			'email' => 'foo2',
			'username' => 'foo2',
			'name' => 'foo',
			'password' => 'foo',
		]);
		
		$this->followRepo->create([
			'user_id' => '1',
			'followable_type' => 'User',
			'followable_id'   => '2',
		]);
		$this->followRepo->create([
			'user_id' => '1',
			'followable_type' => 'User',
			'followable_id'   => '3',
			'is_hushed'       => '1',
		]);
		$this->followRepo->create([
			'user_id' => '1',
			'followable_type' => 'User',
			'followable_id'   => '4',
			'is_hushed'       => '1',
		]);
		$this->followRepo->create([
			'user_id' => '1',
			'followable_type' => 'Designer',
			'followable_id'   => '3',
		]);
		$this->followRepo->create([
			'user_id' => '1',
			'followable_type' => 'Designer',
			'followable_id'   => '4',
			'is_hushed'       => '1',
		]);

		$response = $this->call(
			'GET',
			'/me/follows',
			['is_hushed' => '1']
		);

		$this->assertResponseStatus(200);
		$this->validatePaginatedFollowsDataStructure($response->getData(), 3);
	}

	public function testFollowersIndex()
	{
		$this->mockLogIn(1);

		Designer::create([ // Designer #1
			'user_id' => '1',
			'title'   => 'Foo'
		]);
		User::create([ // User #3
			'email' => 'foo',
			'username' => 'foo',
			'name' => 'foo',
			'password' => 'foo',
		]);
		User::create([ // User #4
			'email' => 'foo2',
			'username' => 'foo2',
			'name' => 'foo',
			'password' => 'foo',
		]);
		User::create([ // User #5
			'email' => 'foo3',
			'username' => 'foo3',
			'name' => 'foo',
			'password' => 'foo',
		]);
		
		// Everybody loves User #1
		$this->followService->createFollow([
			'user_id' => '2',
			'followable_type' => 'User',
			'followable_id'   => '1',
		]);
		$this->followService->createFollow([
			'user_id' => '3',
			'followable_type' => 'User',
			'followable_id'   => '1',
		]);
		$this->followService->createFollow([
			'user_id' => '4',
			'followable_type' => 'User',
			'followable_id'   => '1',
		]);
		// User #2 followers
		$this->followService->createFollow([
			'user_id' => '1',
			'followable_type' => 'User',
			'followable_id'   => '2',
		]);
		$this->followService->createFollow([
			'user_id' => '3',
			'followable_type' => 'User',
			'followable_id'   => '2',
		]);
		// Designer #1 followers
		$this->followService->createFollow([
			'user_id' => '3',
			'followable_type' => 'Designer',
			'followable_id'   => '1',
		]);
		$this->followService->createFollow([
			'user_id' => '4',
			'followable_type' => 'Designer',
			'followable_id'   => '1',
		]);

		$response = $this->call(
			'GET',
			'/me/followers'
		);

		$this->assertResponseStatus(200);
		$this->validatePaginatedDataStructure($response->getData(), 3);

		// now test that alternate uri syntax does the same
		$response = $this->call(
			'GET',
			'/users/1/followers'
		);

		$this->assertResponseStatus(200);
		$this->validatePaginatedDataStructure($response->getData(), 3);

		// now test getting another user's followers
		$response = $this->call(
			'GET',
			'/users/2/followers'
		);

		$this->assertResponseStatus(200);
		$this->validatePaginatedDataStructure($response->getData(), 2);

		// now test getting a designer's followers
		$response = $this->call(
			'GET',
			'/designers/1/followers'
		);

		$this->assertResponseStatus(200);
		$this->validatePaginatedDataStructure($response->getData(), 2);
	}

	public function testAddIsFollowedInfo()
	{
		Designer::create([ // Designer #1
			'user_id' => '1',
			'title'   => 'Foo'
		]);
		Designer::create([
			'user_id' => '2',
			'title'   => 'Bar'
		]);
		Designer::create([
			'user_id' => '3',
			'title'   => 'Baz'
		]);
		User::create([ // User #3
			'email' => 'foo',
			'username' => 'foo',
			'name' => 'foo',
			'password' => 'foo',
		]);
		User::create([ // User #4
			'email' => 'foo2',
			'username' => 'foo2',
			'name' => 'foo',
			'password' => 'foo',
		]);
		User::create([ // User #5
			'email' => 'foo3',
			'username' => 'foo3',
			'name' => 'foo',
			'password' => 'foo',
		]);

		$this->followRepo->create([
			'user_id' => '1',
			'followable_type' => 'User',
			'followable_id'   => '2',
		]);
		$this->followRepo->create([
			'user_id' => '1',
			'followable_type' => 'User',
			'followable_id'   => '4',
		]);
		$this->followRepo->create([
			'user_id' => '1',
			'followable_type' => 'Designer',
			'followable_id'   => '1',
		]);
		$this->followRepo->create([
			'user_id' => '1',
			'followable_type' => 'Designer',
			'followable_id'   => '3',
		]);
		$this->followRepo->create([
			'user_id' => '2',
			'followable_type' => 'Designer',
			'followable_id'   => '2',
		]);
		$this->followRepo->create([
			'user_id' => '2',
			'followable_type' => 'Designer',
			'followable_id'   => '3',
		]);

		// 5 users. user 1 is following users #2 and 4
		$users = User::all()->toArray();
		$this->followService->addIsFollowedInfo($users, '1');

		$this->assertGreaterThan(4, count($users));
		$this->assertEmpty(   $users[0]['is_followed']);
		$this->assertNotEmpty($users[1]['is_followed']);
		$this->assertEmpty(   $users[2]['is_followed']);
		$this->assertNotEmpty($users[3]['is_followed']);
		$this->assertEmpty(   $users[4]['is_followed']);

		// 3 designers. user 1 is following designers #1 and 3
		$designers = Designer::all()->toArray();
		$this->followService->addIsFollowedInfo($designers, '1');

		$this->assertTrue(is_array($designers));
		$this->assertGreaterThan(2, count($designers));
		$this->assertNotEmpty($designers[0]['is_followed']);
		$this->assertEmpty(   $designers[1]['is_followed']);
		$this->assertNotEmpty($designers[2]['is_followed']);

		// 3 designers. user 2 is following designers #2 and 3
		$designers = Designer::all()->toArray();
		$this->followService->addIsFollowedInfo($designers, '2');

		$this->assertTrue(is_array($designers));
		$this->assertGreaterThan(2, count($designers));
		$this->assertEmpty(   $designers[0]['is_followed']);
		$this->assertNotEmpty($designers[1]['is_followed']);
		$this->assertNotEmpty($designers[2]['is_followed']);

		// now with a mix of users and designers
		$users = User::all()->toArray();
		$designers = Designer::all()->toArray();
		$items = array_merge($users, $designers);
		$this->followService->addIsFollowedInfo($items, '1');

		$this->assertTrue(is_array($items));
		$this->assertGreaterThan(6, count($items));
		// Users #2 and 4
		$this->assertEmpty(   $items[0]['is_followed']);
		$this->assertNotEmpty($items[1]['is_followed']);
		$this->assertEmpty(   $items[2]['is_followed']);
		$this->assertNotEmpty($items[3]['is_followed']);
		$this->assertEmpty(   $items[4]['is_followed']);
		// Designers #1 and 3
		$this->assertNotEmpty($items[5]['is_followed']);
		$this->assertEmpty(   $items[6]['is_followed']);
		$this->assertNotEmpty($items[7]['is_followed']);
	}

	public function testDeleteFollowUnauthorized()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'DELETE',
				'FollowController@destroy',
				[ 'follows' => '3' ]
			);

			$this->fail('User with no permissions able to delete another user\'s follow: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testDeleteOwnFollow()
	{
		$this->mockLogIn(1);

		$this->followService->createFollow([
			'user_id' => '1',
			'followable_type' => 'User',
			'followable_id'   => '2',
		]);

		$response = $this->action(
			'DELETE',
			'FollowController@destroy',
			[ 'follows' => '1' ]
		);
		$this->assertResponseStatus(204);

		$result = $this->followRepo->find(1);
		$this->assertNull($result);
	}

	public function testDeleteOtherUsersFollow()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['follow.delete']);

		$response = $this->action(
			'DELETE',
			'FollowController@destroy',
			[ 'follows' => '3' ]
		);
		$this->assertResponseStatus(204);

		$result = $this->followRepo->find(3);
		$this->assertNull($result);
	}

	public function validateFollowDataStructure($data)
	{
		$this->assertTrue(is_object($data));
		$this->assertObjectHasAttribute('id', $data);
		$this->assertObjectHasAttribute('followable_type', $data);
		$this->assertObjectHasAttribute('followable_id', $data);
	}

	public function validatePaginatedFollowsDataStructure($data, $num_items=null)
	{
		$data = $this->validatePaginatedDataStructure($data, $num_items);

		$this->validateFollowDataStructure($data[0]);
	}

	public function validatePaginatedDataStructure($data, $num_items=null)
	{
		$this->assertTrue(is_object($data));
		$this->assertObjectHasAttribute('total', $data);
		$this->assertObjectHasAttribute('per_page', $data);
		$this->assertObjectHasAttribute('data', $data);
		$data = $data->data;
		$this->assertTrue(is_array($data));

		if (isset($num_items))
			$this->assertEquals($num_items, count($data));

		return $data;
	}
}
