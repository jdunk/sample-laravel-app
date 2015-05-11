<?php namespace Acme\Storage\Contest;

use Acme\Model\Eloquent\JudgeContestant;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentJudgeContestantRepository 
extends EloquentBaseRepository
implements JudgeContestantRepository {

	public function __construct(JudgeContestant $model)
	{
		$this->model = $model;
	}
} 
