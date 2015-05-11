<?php

use \Acme\Model\Eloquent\ContestRegion;

class ContestRegionTest extends TestCase
{
	protected $contestRegionRepo;

	public function setUp()
	{
		parent::setUp();
		$this->contestRegionRepo = App::make('Acme\Storage\Contest\ContestRegionRepository');

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
	}

	public function testContestRegionCreateUnauthorized()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'POST',
				'ContestRegionController@store',
				[
					'title'      => 'Unit Test Contest Region',
					'short_code' => 'just_a_test'
				]
			);

			$this->fail('User with no permissions able to create a contest region: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testContestRegionCreate()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['contest_region.create']);

		$response = $this->action(
			'POST',
			'ContestRegionController@store',
			[
				'title'      => 'Unit Test Contest Region',
				'short_code' => 'just_a_test'
			]
		);
		$data = $response->getData();

		$this->assertTrue(is_object($data));
		$this->assertObjectHasAttribute('id', $data);
		$this->assertObjectHasAttribute('short_code', $data);
		$this->assertEquals('just_a_test', $data->short_code);

		$contest_region = $this->contestRegionRepo->find($data->id);

		$this->assertTrue(is_array($contest_region));
		$this->assertArrayHasKey('short_code', $contest_region);
		$this->assertEquals('just_a_test', $contest_region['short_code']);
	}

	public function testContestRegionEditUnauthorized()
	{
		$this->mockLogIn(1);

		try
		{
			$response = $this->action(
				'PUT',
				'ContestRegionController@update',
				['title' => 'blah', 'short_code' => 'bla']
			);

			$this->fail('User with no permissions able to edit a contest region: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testContestRegionEdit()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['contest_region.update']);

		$edits = [
			[ // change title
				'contest_regions' => '1',
				'title'           => 'This is a Contest Region Title - automated edit test',
			],
			[ // change short_code
				'contest_regions' => '1',
				'short_code'      => 'sac2',
			],
			[ // change both
				'contest_regions' => '1',
				'title'           => 'Contest Region Title - automated edit test - wat?',
				'short_code'      => 'sac',
			],
		];

		foreach ($edits as $edit_data)
		{
			try
			{
				$response = $this->action(
					'PUT',
					'ContestRegionController@update',
					$edit_data
				);
			}
			catch (Acme\Validation\Exception $e)
			{
				$this->fail('Validation error(s) updating contest region: ' . json_encode($e->getErrors()->toArray()));
			}
			catch (Exception $e)
			{
				$this->fail('Unable to update contest region: ' . $e->getMessage());
			}

			$this->assertResponseStatus(200);

			$ret = $this->contestRegionRepo->findOrFail($edit_data['contest_regions']);

			$this->assertTrue(is_array($ret));
			$this->assertArrayHasKey('id', $ret);
			$this->assertEquals($ret['id'], $edit_data['contest_regions']);
			unset($edit_data['contest_regions']);

			foreach ($edit_data as $attr => $val)
			{
				$this->assertArrayHasKey($attr, $ret);
				$this->assertEquals($ret[$attr], $val);
			}
		}

		$invalid_edits = [

			// short_code:unique violation
			[
				'contests'          => '1',
				'short_code'        => 'sfbay',
			],
			// short_code: not alpha_dash
			[
				'contests'          => '1',
				'short_code'        => 'i have spaces! and punctuation :)',
			],
		];

		foreach ($invalid_edits as $edit_data)
		{
			try
			{
				$response = $this->action(
					'PUT',
					'ContestRegionController@update',
					$edit_data
				);

				$this->fail('Contest Region update validation should have failed, but passed: '
					. json_encode($edit_data));
			}
			catch (Acme\Validation\Exception $e)
			{
				$this->assertTrue(true);
			}
		}
	}

	public function testContestRegionsIndex()
	{
		$response = $this->action(
			'GET',
			'ContestRegionController@index'
		);

		$this->assertResponseStatus(200);
		$data = $response->getData();
		$this->assertTrue(is_array($data));
		$this->assertGreaterThan(2, count($data));

		$this->validateContestRegionDataStructure($data[0]);
	}

	public function testContestRegionsShow()
	{
		$response = $this->action(
			'GET',
			'ContestRegionController@show',
			[ 'contest_regions' => '1' ]
		);

		$this->assertResponseStatus(200);
		$data = $response->getData();

		$this->validateContestRegionDataStructure($data);
		$this->assertEquals('sac', $data->short_code);
	}

	public function testContestsShowNotFound()
	{
		try
		{
			$response = $this->action(
				'GET',
				'ContestRegionController@show',
				[ 'contest_regions' => '9999999999' ]
			);

			$this->fail('No exception thrown requesting non-existent contest region.');
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
				'ContestRegionController@destroy',
				[ 'contest_regions' => '3' ]
			);

			$this->fail('User with no permissions able to delete a contest region: Expected 403.');
		}
		catch (Acme\Service\ACL\AuthorizationException $e)
		{
			$this->assertTrue(true);
		}
	}

	public function testDeleteContest()
	{
		$this->mockLogIn(1);
		Config::set('acl.tester', ['contest_region.destroy']);

		$response = $this->action(
			'DELETE',
			'ContestRegionController@destroy',
			[ 'contest_regions' => '1' ]
		);
		$this->assertResponseStatus(204);

		$result = $this->contestRegionRepo->find(1);
		$this->assertNull($result);
	}

	public function validateContestRegionDataStructure($data)
	{
		$this->assertTrue(is_object($data));
		$this->assertObjectHasAttribute('id', $data);
		$this->assertObjectHasAttribute('title', $data);
		$this->assertObjectHasAttribute('short_code', $data);
	}
}
