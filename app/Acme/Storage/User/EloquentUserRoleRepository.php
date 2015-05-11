<?php namespace Acme\Storage\User;

use Acme\Model\Eloquent\UserRole;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentUserRoleRepository 
extends EloquentBaseRepository
implements UserRoleRepository {

	public function __construct(UserRole $model)
	{
		$this->model = $model;
	}

	public function getRolesForUser($userId) {
		$qb = $this->model->newQuery();

		return $qb->where('user_id', $userId)->get()->toArray();
	}
} 
