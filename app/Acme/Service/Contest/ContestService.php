<?php namespace Acme\Service\Contest;

interface ContestService {

	public function createContest(array $attrs);
	public function getContestData($id);
	public function getContestsData();
	public function getOwnerRlacFn($id, $userId);
	public function destroyContest($id);

	public function createContestRegion(array $attrs);
	public function getContestRegionData($id);
	public function getContestRegionsData();
	public function destroyContestRegion($id);
}
