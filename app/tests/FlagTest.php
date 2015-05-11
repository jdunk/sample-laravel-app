<?php

use Acme\Model\Eloquent\User;
use Acme\Model\Eloquent\Comment;
use Acme\Model\Eloquent\Post;
use Acme\Model\Eloquent\Flag;
use Acme\Model\Eloquent\FlaggedItem;

class FlagTest extends TestCase
{
	protected $flagRepo;
	protected $flagService;

	public function setUp()
	{
		parent::setUp();
		$this->flagRepo = App::make('Acme\Storage\Flag\FlagRepository');
		$this->flagService = App::make('Acme\Service\Flag\FlagService');

		// 5 posts
		Post::create(['user_id' => '2']);
		Post::create(['user_id' => '2']);
		Post::create(['user_id' => '2']);
		Post::create(['user_id' => '1']);
		Post::create(['user_id' => '1']);

		Comment::create([
			'user_id' => '2',
			'commentable_type' => 'Post',
			'commentable_id' => '2',
			'text' => 'i am doge',
		]);
		Comment::create([
			'user_id' => '2',
			'commentable_type' => 'Post',
			'commentable_id' => '2',
			'text' => 'so cool',
		]);
		Comment::create([
			'user_id' => '2',
			'commentable_type' => 'Post',
			'commentable_id' => '2',
			'text' => 'such awesome',
		]);
		Comment::create([
			'user_id' => '2',
			'commentable_type' => 'Post',
			'commentable_id' => '2',
			'text' => 'very amaze',
		]);
	}

	public function testFlagCreate()
	{
		$this->mockLogIn(1);

		$response = $this->action(
			'POST',
			'FlagController@store',
			[
				'flaggable_type' => 'Comment',
				'flaggable_id' => '2',
			]
		);

		$this->assertResponseStatus(201);

		$flag = $this->flagRepo->find(1);

		$this->assertTrue(is_array($flag));
		$this->assertArrayHasKey('id', $flag);
		$this->assertArrayHasKey('user_id', $flag);
		$this->assertArrayHasKey('flaggable_type', $flag);
		$this->assertArrayHasKey('flaggable_id', $flag);
		$this->assertEquals('1', $flag['id']);
		$this->assertEquals('1', $flag['user_id']);
		$this->assertEquals('Comment', $flag['flaggable_type']);
		$this->assertEquals('2', $flag['flaggable_id']);

		$data = $response->getData();
		$this->validateFlagDataStructure($data);

		// test that flagging a Post works also
		$response = $this->action(
			'POST',
			'FlagController@store',
			[
				'flaggable_type' => 'Post',
				'flaggable_id' => '2',
			]
		);

		$this->assertResponseStatus(201);

		$flag = $this->flagRepo->find(2);

		$this->assertTrue(is_array($flag));
		$this->assertArrayHasKey('id', $flag);
		$this->assertArrayHasKey('user_id', $flag);
		$this->assertArrayHasKey('flaggable_type', $flag);
		$this->assertArrayHasKey('flaggable_id', $flag);
		$this->assertEquals('2', $flag['id']);
		$this->assertEquals('1', $flag['user_id']);
		$this->assertEquals('Post', $flag['flaggable_type']);
		$this->assertEquals('2', $flag['flaggable_id']);

		$data = $response->getData();
		$this->validateFlagDataStructure($data);
	}

	public function testFlaggedItem()
	{
		$this->mockLogIn(1);

		$response = $this->action(
			'POST',
			'FlagController@store',
			[
				'flaggable_type' => 'Comment',
				'flaggable_id' => '2',
			]
		);

		$this->assertResponseStatus(201);

		try
		{
			$flagged_item = FlaggedItem::whereFlaggable('Comment','2')->firstOrFail()->toArray();
		}
		catch (Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$this->fail('flagged_item record not created.');
		}

		$this->validateFlaggedItemHash($flagged_item);
		$this->assertEquals('Comment', $flagged_item['flaggable_type']);
		$this->assertEquals('2', $flagged_item['flaggable_id']);
		$this->assertEquals('1', $flagged_item['flag_count']);
		$this->assertEquals('2', $flagged_item['offending_user_id']);
		$this->assertNull($flagged_item['severity']);
		$this->assertNull($flagged_item['action']);
		$this->assertNull($flagged_item['action_user_id']);
		$this->assertNull($flagged_item['action_created_at']);

		$this->mockLogIn(2);

		$response = $this->action(
			'POST',
			'FlagController@store',
			[
				'flaggable_type' => 'Comment',
				'flaggable_id' => '2',
			]
		);

		$this->assertResponseStatus(201);

		try
		{
			$flagged_item = FlaggedItem::whereFlaggable('Comment','2')->firstOrFail()->toArray();
		}
		catch (Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$this->fail('flagged_item record not found after 2nd flagging of same item.');
		}

		$this->validateFlaggedItemHash($flagged_item);
		$this->assertEquals('Comment', $flagged_item['flaggable_type']);
		$this->assertEquals('2', $flagged_item['flaggable_id']);
		$this->assertEquals('2', $flagged_item['flag_count']);
		$this->assertEquals('2', $flagged_item['offending_user_id']);
		$this->assertNull($flagged_item['severity']);
		$this->assertNull($flagged_item['action']);
		$this->assertNull($flagged_item['action_user_id']);
		$this->assertNull($flagged_item['action_created_at']);
	}

	public function testFlaggableTypeError()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'POST',
				'FlagController@store',
				[
					'flaggable_type' => 'Flag', // you can't flag a flag, silly!
					'flaggable_id' => '1',
				]
			);

			$this->fail('Expected Validation exception for invalid flaggable_type; none thrown.');
		}
		catch (Acme\Validation\Exception $e)
		{
			$errors = $e->getErrors()->toArray();

			if (! isset($errors['flaggable_type']))
				$this->fail('Validation exception was thrown, but expected error for invalid flaggable_type was not found.');

			$this->assertTrue(true);
		}
	}

	public function testNonExistentFlaggableError()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'POST',
				'FlagController@store',
				[
					'flaggable_type' => 'Post',
					'flaggable_id' => '918241274',
				]
			);

			$this->fail('Expected Validation exception for invalid [non-existent] flaggable; none thrown.');
		}
		catch (Acme\Validation\Exception $e)
		{
			$errors = $e->getErrors()->toArray();

			if (! isset($errors['flaggable_id']))
				$this->fail('Validation exception was thrown, but expected error for invalid flaggable_id was not found.');

			$this->assertTrue(true);
		}
	}

	public function testDuplicateFlagError()
	{
		$this->mockLogIn(1);

		$response = $this->action(
			'POST',
			'FlagController@store',
			[
				'flaggable_type' => 'Comment',
				'flaggable_id' => '2',
			]
		);

		try
		{
			$response = $this->action(
				'POST',
				'FlagController@store',
				[
					'flaggable_type' => 'Comment',
					'flaggable_id' => '2',
				]
			);

			$this->fail('Expected Validation exception for invalid [duplicate] flag; none thrown.');
		}
		catch (Acme\Validation\Exception $e)
		{
			$errors = $e->getErrors()->toArray();

			if (! isset($errors['user_id']))
				$this->fail('Validation exception was thrown, but expected error on user_id was not found.');

			$this->assertTrue(true);
		}
	}

	public function testFlaggedItemsIndexUnauthorized()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->call(
				'GET',
				'/flagged_items'
			);

			$this->fail('User with no permissions able to view flagged_items index: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testFlaggedItemsIndex()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['flag.read']);

		$this->createSomeFlags();

		$response = $this->call(
			'GET',
			'/flagged_items'
		);

		$this->assertResponseStatus(200);
		$this->validatePaginatedFlaggedItemsDataStructure($response->getData(), 5);
	}

	public function testFlaggedItemShowUnauthorized()
	{
		$this->mockLogIn(1);
		$this->createSomeFlags();

		try
		{
			$response = $this->call(
				'GET',
				'/flagged_items/1'
			);

			$this->fail('User with no permissions able to view flagged_item detail: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testFlaggedItemShowNonExistent()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['flag.read']);

		$this->createSomeFlags();

		try
		{
			$response = $this->call(
				'GET',
				'/flagged_items/919129499999'
			);

			$this->fail('Flagged item update: expected ModelNotFoundException; none thrown');
		}
		catch (Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testFlaggedItemShow()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['flag.read']);

		$this->createSomeFlags();

		$response = $this->call(
			'GET',
			'/flagged_items/1' // a flagged Comment
		);

		$this->assertResponseStatus(200);
		$this->validateFlaggedItemDataStructureHydrated($response->getData());

		$response = $this->call(
			'GET',
			'/flagged_items/4' // a flagged Post
		);

		$this->assertResponseStatus(200);
		$this->validateFlaggedItemDataStructureHydrated($response->getData());
	}

	public function testFlaggedItemUpdateUnauthorized()
	{
		$this->mockLogIn(1);

		$this->flagService->createFlag([
			'user_id' => '2',
			'flaggable_type' => 'Comment',
			'flaggable_id'   => '2',
		]);

		try
		{
			$response = $this->action(
				'PUT',
				'FlagController@update',
				['id' => '1', 'severity' => '1']
			);

			$this->fail('User with no permissions able to update a flagged_item: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testFlaggedItemUpdateNonExistent()
	{
		// Also tests that the 'flag.update' perm is required.
		// Without any perm check, previous test will fail.
		// If wrong perm is checked, this test will fail.
		$this->mockLogIn(1);
		Config::set('acl.tester', ['flag.update']);

		try
		{
			$response = $this->action(
				'PUT',
				'FlagController@update',
				[
					'id'   => '981723941999',
				]
			);

			$this->fail('Flagged item update: expected ModelNotFoundException; none thrown');
		}
		catch (Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testFlaggedItemUpdate()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['flag.update']);

		$this->flagService->createFlag([
			'user_id' => '1',
			'flaggable_type' => 'Comment',
			'flaggable_id'   => '2',
		]);

		$valid_edits = [
			['id' => '1', 'severity' => '1'],
			['id' => '1', 'severity' => '10', 'action' => 'Sentenced user to listen to 13 hours of nyan cat.'],
		];

		foreach ($valid_edits as $edit_data)
		{
			$response = $this->action(
				'PUT',
				'FlagController@update',
				$edit_data
			);

			$this->assertResponseStatus(200);

			$res = FlaggedItem::findOrFail(1)->toArray();

			$this->assertTrue(is_array($res));

			foreach ($edit_data as $k => $v)
			{
				$this->assertArrayHasKey($k, $res);
				$this->assertEquals($edit_data[$k], $res[$k]);
			}
		}

		$invalid_edits = [
			[
				'id'       => '1',
				'severity' => '11',
			],
			[
				'id'       => '1',
				'severity' => '0',
			],
			[
				'id'       => '1',
				'severity' => 'high',
			],
			[
				'id'       => '1',
				'action'   => str_repeat('1234567890', 26), // exceeds max 255 chars
			],
		];

		foreach ($invalid_edits as $edit_data)
		{
			try
			{
				$response = $this->action(
					'PUT',
					'FlagController@update',
					$edit_data
				);

				$this->fail('Flag update validation should have failed, but passed: '
					. json_encode($edit_data));
			}
			catch (Acme\Validation\Exception $e)
			{
				$this->assertTrue(true);
			}
		}
	}

	public function validateFlagDataStructure($data)
	{
		$this->assertTrue(is_object($data));
		$this->assertObjectHasAttribute('id', $data);
		$this->assertObjectHasAttribute('user_id', $data);
		$this->assertObjectHasAttribute('flaggable_type', $data);
		$this->assertObjectHasAttribute('flaggable_id', $data);
	}

	public function validateFlaggedItemHash($data)
	{
		$this->assertTrue(is_array($data));
		$this->assertArrayHasKey('id', $data);
		$this->assertArrayHasKey('flaggable_type', $data);
		$this->assertArrayHasKey('flaggable_id', $data);
		$this->assertArrayHasKey('flag_count', $data);
		$this->assertArrayHasKey('offending_user_id', $data);
		$this->assertArrayHasKey('severity', $data);
		$this->assertArrayHasKey('action', $data);
		$this->assertArrayHasKey('action_user_id', $data);
		$this->assertArrayHasKey('action_created_at', $data);
	}

	public function validateFlaggedItemDataStructure($data)
	{
		$this->assertTrue(is_object($data));
		$this->assertObjectHasAttribute('id', $data);
		$this->assertObjectHasAttribute('flaggable_type', $data);
		$this->assertObjectHasAttribute('flaggable_id', $data);
		$this->assertObjectHasAttribute('flag_count', $data);
		$this->assertObjectHasAttribute('offending_user_id', $data);
		$this->assertObjectHasAttribute('severity', $data);
		$this->assertObjectHasAttribute('action', $data);
		$this->assertObjectHasAttribute('action_user_id', $data);
		$this->assertObjectHasAttribute('action_created_at', $data);
	}

	public function validateFlaggedItemDataStructureHydrated($data)
	{
		$this->validateFlaggedItemDataStructure($data);

		$this->assertObjectHasAttribute('flaggable', $data);
		$this->assertTrue(is_object($data->flaggable));

		$this->assertObjectHasAttribute('user', $data->flaggable);
		$this->assertTrue(is_object($data->flaggable->user));

		$this->assertObjectHasAttribute('username', $data->flaggable->user);
	}

	public function validatePaginatedFlaggedItemsDataStructure($data, $num_items=null)
	{
		$data = $this->validatePaginatedDataStructure($data, $num_items);

		$this->validateFlaggedItemDataStructure($data[0]);
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

	private function createSomeFlags()
	{
		$this->flagService->createFlag([
			'user_id' => '1',
			'flaggable_type' => 'Comment',
			'flaggable_id'   => '2',
		]);
		$this->flagService->createFlag([
			'user_id' => '2',
			'flaggable_type' => 'Comment',
			'flaggable_id'   => '2',
		]);
		$this->flagService->createFlag([
			'user_id' => '1',
			'flaggable_type' => 'Comment',
			'flaggable_id'   => '3',
		]);
		$this->flagService->createFlag([
			'user_id' => '2',
			'flaggable_type' => 'Comment',
			'flaggable_id'   => '4',
		]);
		$this->flagService->createFlag([
			'user_id' => '2',
			'flaggable_type' => 'Post',
			'flaggable_id'   => '1',
		]);
		$this->flagService->createFlag([
			'user_id' => '1',
			'flaggable_type' => 'Post',
			'flaggable_id'   => '2',
		]);
	}
}
