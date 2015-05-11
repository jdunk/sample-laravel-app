<?php namespace Acme\Service\ACL;

class ARO {
	protected $roles = [];
	protected $permissions = [];

	public function __construct($roles = array(), $permissions = array()) {
		if (!$roles) $roles = [];
		
		$this->roles = $roles;
		$this->permissions = $this->mergePermissions($permissions, $roles);
	}

	/**
	 * Can
	 *
	 *
	 * Returns false when slug is not permitted in user permissions OR when none 
	 * of the additional arguments passed are trueish.
	 * 
	 * @param  string $slug ACL dot-notated slug
	 * @param  mixed n/a additional args can be boolean or functions to eval
	 * @return bool       true if can, false if cannot
	 */
	public function can($slug) {
		$callable = [];

		$numargs = func_num_args();
		if ($numargs > 1) {
			$arg_list = func_get_args();
			
			for ($i = 1; $i < $numargs; $i++) {
				if (is_callable($arg_list[$i])) {
					$callable[] = $arg_list[$i];
				} elseif ($arg_list[$i]) {
					// already trueish
					return true;
				}
			}
		}

		// look for exact matches
		if (in_array($slug, $this->permissions))
			return true;

		// sift through wildcards
		if (strpos($slug, '.') !== false) {
			if (in_array('*.*', $this->permissions))
				return true;

			$wcSlug = strtok($slug, '.') . '.*';
			if (in_array($wcSlug, $this->permissions))
				return true;
		}

		// now we are sure it's worth running callables...
		if (count($callable) === 0)
			return false;

		foreach ($callable as $idx => $fn) {
			if (call_user_func($fn)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Must
	 *
	 * Throws AuthorizationException when slug is not permitted in user 
	 * permissions OR when none of the additional arguments passed are trueish.
	 * 
	 * @param  string $slug dot-notated action being attempted
	 * @throws AuthorizationException
	 */
	public function must($slug) {
		if (!call_user_func_array(array($this, 'can'), func_get_args())) 
			throw new AuthorizationException('Access Denied');
	}

	public function is($role) {
		foreach($this->roles as $roleData) {
			if ($roleData['role'] === $role)
				return true;
		}

		return false;
	}

	public function mergePermissions($permissions, $roles) {
		$result = [];

		if (empty($roles))
			$roles = [];

		foreach ($roles as $idx => $role) {
			$result = array_merge($result, $this->extractPermissionsForRole($permissions, $role['role']));
		}

		return $result;
	}

	public function extractPermissionsForRole($permissions, $role) {
		$result = [];

		if (empty($permissions[$role]))
			return [];

		foreach ($permissions[$role] as $slug) {
			if (strpos($slug, 'merge:') === 0) {
				$mergeRole = trim(str_replace('merge:', '', $slug));
				$result = array_merge($result, $this->extractPermissionsForRole($permissions, $mergeRole));
				continue;
			}

			$result[] = $slug;
		}

		return $result;
	}
}
