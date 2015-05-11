<?php

use \Acme\Model\Eloquent\ContestRegion;

class ContestTest extends TestCase
{
	protected $contestRepo;
	protected $contestService;

	public function setUp()
	{
		parent::setUp();
		$this->contestRepo = App::make('Acme\Storage\Contest\ContestRepository');
		$this->contestService = App::make('Acme\Service\Contest\ContestService');

		DB::table('contests')->truncate();
		DB::table('contest_regions')->truncate();

		// TODO: Replace below with service to ensure we are working with *valid* test data
		ContestRegion::create([
			'title'      => 'Greater Sacramento',
			'short_code' => 'sac',
		]);
		ContestRegion::create([
			'title'      => 'SF Bay Area',
			'short_code' => 'sfbay',
		]);
		ContestRegion::create([
			'title'      => 'Los Angeles metropolitan area',
			'short_code' => 'losangeles',
		]);

		$this->contestService->createContest([
			'user_id'           => '1',
			'contest_region_id' => '2',
			'year'              => '3001',
			'season'            => 'S',
			'title'             => 'Bay Area Contest of Summer 3001',
		]);
		$this->contestService->createContest([
			'user_id'           => '1',
			'contest_region_id' => '2',
			'year'              => '3001',
			'season'            => 'F',
			'title'             => 'Bay Area Contest of Fall 3001',
		]);
		$this->contestService->createContest([
			'user_id'           => '2',
			'contest_region_id' => '1',
			'year'              => '3002',
			'season'            => 'S',
			'title'             => 'Sac Contest of Summer 3002',
		]);
		$this->contestService->createContest([
			'user_id'           => '2',
			'contest_region_id' => '1',
			'year'              => '3002',
			'season'            => 'F',
			'title'             => 'Sac Contest of Fall 3002',
		]);
	}

	public function testContestCreateUnauthorized()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'POST',
				'ContestController@store',
				[
					'year' => '2027',
					'season' => 'F',
					'title' => 'A Contest',
					'contest_region_id' => '1',
				]
			);

			$this->fail('User with no permissions able to create a contest: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testContestCreate()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['contest.create']);

		try
		{
			$response = $this->action(
				'POST',
				'ContestController@store',
				[
					'year' => '2027',
					'season' => 'F',
					'title' => 'Image',
					'contest_region_id' => '1',
				]
			);
		}
		catch (Exception $e)
		{
			$this->fail('Unable to create contest: ' . $e->getMessage());
		}

		$this->assertResponseStatus(201);

		$contest_find_result = $this->contestRepo->find(1);

		$this->assertTrue(is_array($contest_find_result));
		$this->assertArrayHasKey('id', $contest_find_result);
		$this->assertArrayHasKey('user_id', $contest_find_result);
		$this->assertEquals(1, $contest_find_result['id']);
		$this->assertEquals(1, $contest_find_result['user_id']);
	}

	public function testActivateContestUnauthorized()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'PUT',
				'ContestController@update',
				[
					'contests' => '3',
					'is_active' => '1'
				]
			);

			$this->fail('User with no permissions able to activate a contest region: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testActivateOwnContest()
	{
		$this->mockLogIn(1);

		$response = $this->action(
			'PUT',
			'ContestController@update',
			[
				'contests' => '1',
				'is_active' => '1'
			]
		);

		$this->assertResponseStatus(200);

		$contest = $this->contestRepo->findOrFail(1);

		$this->assertTrue(is_array($contest));
		$this->assertArrayHasKey('is_active', $contest);
		$this->assertEquals('1', $contest['is_active']);
	}

	public function testActivateOtherUsersContest()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['contest.update']);

		$response = $this->action(
			'PUT',
			'ContestController@update',
			[
				'contests' => '3',
				'is_active' => '1'
			]
		);

		$this->assertResponseStatus(200);

		$contest = $this->contestRepo->findOrFail(3);

		$this->assertTrue(is_array($contest));
		$this->assertArrayHasKey('is_active', $contest);
		$this->assertEquals('1', $contest['is_active']);

		// test deactivate also
		$response = $this->action(
			'PUT',
			'ContestController@update',
			[
				'contests' => '3',
				'is_active' => '0'
			]
		);

		$this->assertResponseStatus(200);

		$contest = $this->contestRepo->findOrFail(3);

		$this->assertTrue(is_array($contest));
		$this->assertArrayHasKey('is_active', $contest);
		$this->assertEquals('0', $contest['is_active']);
	}

	public function testContestUpdate()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['contest.update']);

		// seeded contests:
		/*
		[
			'id'                => '1',
			'user_id'           => '1',
			'contest_region_id' => '2',
			'year'              => '3001',
			'season'            => 'S',
			'title'             => 'Bay Area Contest of Summer 3001',
		],
		[
			'id'                => '2',
			'user_id'           => '1',
			'contest_region_id' => '2',
			'year'              => '3001',
			'season'            => 'F',
			'title'             => 'Bay Area Contest of Fall 3001',
		],
		[
			'id'                => '3',
			'user_id'           => '2',
			'contest_region_id' => '1',
			'year'              => '3002',
			'season'            => 'S',
			'title'             => 'Sac Contest of Summer 3002',
		],
		[
			'id'                => '4',
			'user_id'           => '2',
			'contest_region_id' => '1',
			'year'              => '3002',
			'season'            => 'F',
			'title'             => 'Sac Contest of Fall 3002',
		]
		*/
 
		$edits = [
			[ // only change year
				'contests'          => '1',
				'contest_region_id' => '2',
				'year'              => '3000',
				'season'            => 'S',
				'title'             => 'Bay Area Contest of Summer 3001',
			],
			[ // change year and season
				'contests'          => '1',
				'contest_region_id' => '2',
				'year'              => '4000',
				'season'            => 'F',
				'title'             => 'Bay Area Contest of Summer 3001',
			],
			[ // change year, season, and contest_region_id
				'contests'          => '1',
				'contest_region_id' => '1',
				'year'              => '3000',
				'season'            => 'S',
				'title'             => 'Bay Area Contest of Summer 3001',
			],
			[ // change all
				'contests'          => '1',
				'contest_region_id' => '2',
				'year'              => '4000',
				'season'            => 'F',
				'title'             => 'unit test Contest Title of Fall 3001',
			],
			[ // reset
				'contests'          => '1',
				'contest_region_id' => '2',
				'year'              => '3001',
				'season'            => 'S',
				'title'             => 'Bay Area Contest of Summer 3001',
			],
			// Now just 1 field at a time
			[
				'contests'          => '1',
				'contest_region_id' => '1',
			],
			[
				'contests' => '4',
				'year'     => '3001',
			],
			[
				'contests' => '3',
				'season'   => 'F',
			],
			[
				'contests' => '1',
				'title'    => 'unit test Some Other Contest Test Title',
			],
			// reset
			[
				'contests' => '3',
				'season'   => 'S',
			],
			[
				'contests' => '4',
				'year'     => '3002',
			],
			[ // set up for potential collision test
				'contests'          => '1',
				'contest_region_id' => '1',
				'year'              => '3001',
				'season'            => 'S',
				'title'             => 'Sac Contest of Summer 3001',
			],
			// Now test submitting only some fields.
			// This helps ensure no false collisions detected.
			/*
				unique keys now:
				1:1-3001-S
				2:2-3001-F
				3:1-3002-S
				4:1-3002-F
			*/
			[ // contest_region_id and year
				'contests'          => '1',
				'contest_region_id' => '2',
				'year'              => '3002',
			],
			/*
				1:2-3002-S
				2:2-3001-F
				3:1-3002-S
				4:1-3002-F
			*/
			[ // contest_region_id and season
				'contests'          => '2',
				'contest_region_id' => '1',
				'season'            => 'S',
			],
			/*
				1:2-3002-S
				2:1-3001-S
				3:1-3002-S
				4:1-3002-F
			*/
			[ // year and season
				'contests' => '1',
				'year'     => '3001',
				'season'   => 'F',
			],
			/*
				1:2-3002-F
				2:1-3001-S
				3:1-3002-S
				4:1-3002-F
			*/
			// reset
			[
				'contests' => '2',
				'year'     => '3001',
			],
			[
				'contests'          => '2',
				'contest_region_id' => '2',
			],
			/*
				1:2-3001-F
				2:2-3001-S
				3:1-3002-S
				4:1-3002-F
			*/
		];

		foreach ($edits as $edit_data)
		{
			try
			{
				$response = $this->action(
					'PUT',
					'ContestController@update',
					$edit_data
				);
			}
			catch (Acme\Validation\Exception $e)
			{
				$this->fail('Validation error(s) updating contest: ' . json_encode($e->getErrors()->toArray()));
			}

			$this->assertResponseStatus(200);

			$contest = $this->contestRepo->findOrFail($edit_data['contests']);

			$this->assertTrue(is_array($contest));
			$this->assertArrayHasKey('id', $contest);
			$this->assertEquals($contest['id'], $edit_data['contests']);
			unset($edit_data['contests']);

			foreach ($edit_data as $attr => $val)
			{
				$this->assertArrayHasKey($attr, $contest);
				$this->assertEquals($val, $contest[$attr]);
			}
		}

		/* current unique keys:
			1:2-3001-F
			2:2-3001-S
			3:1-3002-S
			4:1-3002-F
		*/
		$invalid_edits = [

			// These help ensure unique_with validator uses values
			// of existing record for fields not submitted.
			[
				'contests'          => '1',
				'contest_region_id' => '1',
				'year'              => '3002',
			],
			[
				'contests'          => '3',
				'season'            => 'F',
			],
			[
				'contests'          => '1',
				'season'            => 'W',
			],
			[
				'contests'          => '1',
				'is_active'         => '2',
			],
		];

		foreach ($invalid_edits as $edit_data)
		{
			try
			{
				$response = $this->action(
					'PUT',
					'ContestController@update',
					$edit_data
				);

				$this->fail('Contest update validation should have failed, but passed: '
					. json_encode($edit_data));
			}
			catch (Acme\Validation\Exception $e)
			{
				$this->assertTrue(true);
			}
		}
	}

	public function testContestsIndex()
	{
		$response = $this->action(
			'GET',
			'ContestController@index'
		);

		$this->assertResponseStatus(200);
		$data = $response->getData();
		$this->assertTrue(is_array($data));
		$this->assertEquals(4, count($data));

		$this->validateContestDataStructure($data[0]);
	}

	public function testContestsShow()
	{
		$response = $this->action(
			'GET',
			'ContestController@show',
			[ 'contests' => '1' ]
		);

		$this->assertResponseStatus(200);

		$this->validateContestDataStructure($response->getData());
	}

	public function testContestsShowNotFound()
	{
		try
		{
			$response = $this->action(
				'GET',
				'ContestController@show',
				[ 'contests' => '9123941237' ]
			);

			$this->fail('No exception thrown requesting non-existent contest.');
		}
		catch (Illuminate\Database\Eloquent\ModelNotFoundException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testDeleteContestUnauthorized()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'DELETE',
				'ContestController@destroy',
				[ 'contests' => '3' ]
			);

			$this->fail('Auth exception not thrown when trying to delete another user\'s contest.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testDeleteOwnContest()
	{
		$this->mockLogIn(2);

		$response = $this->action(
			'DELETE',
			'ContestController@destroy',
			[ 'contests' => '3' ]
		);
		$this->assertResponseStatus(204);

		$result = $this->contestRepo->find(3);
		$this->assertNull($result);
	}

	public function testDeleteOtherUsersContest()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['contest.delete']);

		$response = $this->action(
			'DELETE',
			'ContestController@destroy',
			[ 'contests' => '3' ]
		);
		$this->assertResponseStatus(204);

		$result = $this->contestRepo->find(3);
		$this->assertNull($result);
	}

	public function validateContestDataStructure($data)
	{
		$this->assertTrue(is_object($data));
		$this->assertObjectHasAttribute('id', $data);
		$this->assertObjectHasAttribute('title', $data);
		$this->assertObjectHasAttribute('year', $data);
		$this->assertObjectHasAttribute('season', $data);
		$this->assertObjectHasAttribute('is_active', $data);
		$this->assertObjectHasAttribute('created_at', $data);
		$this->assertObjectHasAttribute('user_id', $data);
		$this->assertObjectHasAttribute('contest_region', $data);

		$data = $data->contest_region;
		$this->assertTrue(is_object($data));
		$this->assertObjectHasAttribute('id', $data);
		$this->assertObjectHasAttribute('title', $data);
		$this->assertObjectHasAttribute('short_code', $data);
	}
}
