<?php namespace Acme\Storage\Contest;

use Acme\Model\Eloquent\ContestJudge;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentContestJudgeRepository 
extends EloquentBaseRepository
implements ContestJudgeRepository {

	public function __construct(ContestJudge $model)
	{
		$this->model = $model;
	}
} 
