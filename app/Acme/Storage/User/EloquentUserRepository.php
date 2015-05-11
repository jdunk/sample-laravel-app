<?php namespace Acme\Storage\User;

use Acme\Model\Eloquent\User;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentUserRepository 
extends EloquentBaseRepository
implements UserRepository {

	public function __construct(User $model)
	{
		$this->model = $model;
	}
} 
