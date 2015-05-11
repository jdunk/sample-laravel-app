<?php namespace Acme\Storage\Contest;

use Acme\Model\Eloquent\Judge;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentJudgeRepository 
extends EloquentBaseRepository
implements JudgeRepository {

	public function __construct(Judge $model)
	{
		$this->model = $model;
	}
} 
