<?php namespace Acme\Service\Contest;

use Acme\Validation\Validators\ContestCreate as ContestCreateValidator;
use Acme\Validation\Validators\ContestEdit as ContestEditValidator;
use Acme\Validation\Validators\ContestRegionCreate as ContestRegionCreateValidator;
use Acme\Validation\Validators\ContestRegionEdit as ContestRegionEditValidator;
use Acme\Service\Contest\ContestService;
use Acme\Model\Eloquent\Contest;
use Acme\Model\Eloquent\ContestRegion;

class EloquentContestService implements ContestService {

	protected $creationValidator;
	protected $modificationValidator;
	protected $eloquentContest;
	protected $eloquentContestRegion;

	public function __construct(
		Contest $eloquentContest,
		ContestRegion $eloquentContestRegion,
		ContestCreateValidator $creationValidator,
		ContestEditValidator $modificationValidator)
	{
		$this->eloquentContest = $eloquentContest;
		$this->eloquentContestRegion = $eloquentContestRegion;
		$this->creationValidator = $creationValidator;
		$this->modificationValidator = $modificationValidator;
	}

	public function createContest(array $attrs)
	{
		$this->creationValidator->validate($attrs);

		$contest = $this->eloquentContest->create(
			array_only($attrs, ['user_id', 'contest_region_id', 'title', 'year', 'season'])
		);

		return $this->getContestData($contest->id);
	}

	public function createContestRegion(array $attrs)
	{
		(new ContestRegionCreateValidator)->validate($attrs);

		$contest_region = $this->eloquentContestRegion->create(
			array_only($attrs, ['title', 'short_code'])
		);

		return $this->getContestRegionData($contest_region->id);
	}

	public function updateContest($id, array $attrs)
	{
		$contest = $this->eloquentContest
			->select('year', 'season', 'contest_region_id')
			->findOrFail($id)->toArray();

		// Fill in composite-unique fields with record data if absent from update data.
		// Otherwise unique_with validator will not have enough info and will fail.
		foreach (['year','season','contest_region_id'] as $field)
		{
			if (! isset($attrs[$field]))
				$attrs[$field] = $contest[$field];
		}

		// tell "unique_with" validator to ignore this record
		$this->modificationValidator->rules['contest_region_id'] .= ",$id";

		$this->modificationValidator->validate($attrs);

		$contest = $this->eloquentContest->findOrFail($id);

		$contest->update(array_only($attrs, ['contest_region_id', 'title', 'year', 'season', 'is_active']));

		return $this->getContestData($contest->id);
	}

	public function updateContestRegion($id, array $attrs)
	{
		(new ContestRegionEditValidator)->validate($attrs);

		$contest_region = $this->eloquentContestRegion->findOrFail($id);

		$contest_region->update(array_only($attrs, ['title', 'short_code']));

		return $this->getContestRegionData($contest_region->id);
	}

	public function getContestData($id)
	{
		$contest = $this->eloquentContest->with([
			'contest_region' => function($q) {
				$q->select('id', 'title', 'short_code');
			}])
			->select('id', 'title', 'year', 'season', 'is_active', 'created_at', 'user_id', 'contest_region_id')
			->findOrFail($id)->toArray();

		unset($contest['contest_region_id']);

		return $contest;
	}

	public function getContestRegionData($id)
	{
		return $this->eloquentContestRegion
			->select('id', 'title', 'short_code', 'created_at')
			->findOrFail($id)->toArray();
	}

	public function getContestsData()
	{
		$contests = $this->eloquentContest->with([
			'contest_region' => function($q) {
				$q->select('id', 'title', 'short_code');
			}])
			->select('id', 'title', 'year', 'season', 'is_active', 'created_at', 'user_id', 'contest_region_id')
			->get()->toArray();

		foreach ($contests as &$contest)
			unset($contest['contest_region_id']);

		return $contests;
	}

	public function getContestRegionsData()
	{
		return $this->eloquentContestRegion
			->select('id', 'title', 'short_code', 'created_at')
			->get()->toArray();
	}

	public function getOwnerRlacFn($id, $userId)
	{
		return function() use ($id, $userId) {
			$c = $this->eloquentContest->where('user_id', $userId)
				->where('id', $id)
				->select('id')
				->first();

			if (!$c)
				return false;

			return true;
		};
	}

	public function destroyContest($id)
	{
		$this->eloquentContest->destroy($id);
	}

	public function destroyContestRegion($id)
	{
		$this->eloquentContestRegion->destroy($id);
	}
}
