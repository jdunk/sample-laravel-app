<?php namespace Acme\Storage\Contest;

use Acme\Model\Eloquent\ContestRegion;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentContestRegionRepository 
extends EloquentBaseRepository
implements ContestRegionRepository {

	public function __construct(ContestRegion $model)
	{
		$this->model = $model;
	}
} 
