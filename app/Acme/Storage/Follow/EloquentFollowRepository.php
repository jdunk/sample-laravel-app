<?php namespace Acme\Storage\Follow;

use Acme\Model\Eloquent\Follow;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentFollowRepository 
extends EloquentBaseRepository
implements FollowRepository {

	public function __construct(Follow $model)
	{
		$this->model = $model;
	}
} 
