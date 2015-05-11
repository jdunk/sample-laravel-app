<?php namespace Acme\Storage\User;

interface UserRoleRepository {
	public function getRolesForUser($userId);
}
