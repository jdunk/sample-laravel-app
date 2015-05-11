<?php namespace Acme\Storage\Flag;

use Acme\Model\Eloquent\Flag;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentFlagRepository 
extends EloquentBaseRepository
implements FlagRepository {

	public function __construct(Flag $model)
	{
		$this->model = $model;
	}
} 
