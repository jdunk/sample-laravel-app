<?php

class ContestRepositoriesTest extends TestCase
{
	protected $repos = [
		'Contest\ContestDesignerRepository',
		'Contest\ContestJudgeRepository',
		'Contest\ContestRegionRepository',
		'Contest\ContestRepository',
		'Contest\DesignerVoteRepository',
		'Contest\JudgeContestantRepository',
		'Contest\JudgeRepository',
		'Designer\DesignerRepository'
	];

	public function testRepositoriesCanInstantiate()
	{
		foreach ($this->repos as $repo)
		{
			try
			{
				$obj = App::make('Acme\Storage\\' . $repo);
				$this->assertTrue(true);
			}
			catch (Exception $e)
			{
				$this->fail("Unable to instantiate '$repo': " . $e->getMessage());
			}
		}
	}
}
