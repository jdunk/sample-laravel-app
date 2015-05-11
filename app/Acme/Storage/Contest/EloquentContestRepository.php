<?php namespace Acme\Storage\Contest;

use Acme\Model\Eloquent\Contest;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentContestRepository 
extends EloquentBaseRepository
implements ContestRepository {

	public function __construct(Contest $model)
	{
		$this->model = $model;
	}
} 
