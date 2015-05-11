<?php namespace Acme\Service\Follow;

use Acme\Validation\Validators\FollowCreate as FollowCreateValidator;
use Acme\Validation\Validators\FollowUpdate as FollowUpdateValidator;
use Acme\Service\Follow\FollowService;
use Acme\Model\Eloquent\Follow;
use Acme\Model\Eloquent\User;

class EloquentFollowService implements FollowService {

	protected $eloquentFollow;
	protected $createValidator;
	protected $updateValidator;
	public $itemsPerPage = 15;

	public function __construct(Follow $eloquentFollow, FollowCreateValidator $createValidator, FollowUpdateValidator $updateValidator)
	{
		$this->eloquentFollow = $eloquentFollow;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function createFollow(array $attrs)
	{
		$this->createValidator->validate($attrs);

		// Disallow user from following self
		if ($attrs['user_id'] == $attrs['followable_id'] &&
			$attrs['followable_type'] == 'User')
		{
			$mb = new \Illuminate\Support\MessageBag;
			$mb->add('user_id', 'User not allowed to follow self.');
			throw new \Acme\Validation\Exception('Validation failed', $mb);
		}

		// If followable record doesn't exist
		$classname = 'Acme\Model\Eloquent\\' . $attrs['followable_type'];
		if (! $classname::find($attrs['followable_id']))
		{
			$mb = new \Illuminate\Support\MessageBag;
			$mb->add('followable_id', $attrs['followable_type'] . ' with ID #' . $attrs['followable_id'] . ' not found.');
			throw new \Acme\Validation\Exception('Validation failed', $mb);
		}

		$follow = $this->eloquentFollow->create(
			$attrs,
			['user_id', 'followable_type', 'followable_id']
		);

		return $this->getFollow($follow->id);
	}

	public function updateFollow($id, array $attrs)
	{
		$this->updateValidator->validate($attrs);

		$follow = $this->eloquentFollow->findOrFail($id);

		$follow->update(array_only($attrs, ['is_hushed']));

		return $this->getFollow($follow->id);
	}

	public function getFollow($follow_id)
	{
		$follow = $this->eloquentFollow
			->select('id', 'followable_id', 'followable_type', 'created_at', 'user_id')
			->find($follow_id)->toArray();

		unset($follow['user_id']);

		return $follow;
	}

	public function getFollows($user_id, $hushed_only=false)
	{
		// trigger 404 if invalid user
		User::findOrFail($user_id);

		$qb = $this->eloquentFollow
			->select('id', 'followable_id', 'followable_type', 'created_at')
			->where('user_id', $user_id);

		if ($hushed_only)
			$qb->where('is_hushed', '1');

		return $qb->paginate($this->itemsPerPage)->toArray();
	}

	public function getFollowers($followable_type, $followable_id)
	{
		$qb = $this->eloquentFollow->with([
			'user' => function($q) {
				$q->select('id', 'username', 'name', 'image_small', 'image_large', 'is_active');
			}])
			->select('user_id')
			->where('followable_type', $followable_type)
			->where('followable_id', $followable_id);

		$ret = $qb->paginate($this->itemsPerPage)->toArray();

		// restructure so it's a flat array of user data
		foreach ($ret['data'] as & $item)
		{
			$item = $item['user'];
		}

		return $ret;
	}

	public function addIsFollowedInfo(& $followables, $user_id)
	{
		$followable_ids = [];

		foreach ($followables as $followable)
		{
			if (isset($followable['username']))
				$followable_ids['User'][] = $followable['id'];
			else
				$followable_ids['Designer'][] = $followable['id'];
		}

		if (isset($followable_ids['User']))
		{
			$r = $this->eloquentFollow
				->select('followable_id')
				->where('user_id', $user_id)
				->where('followable_type', 'User')
				->whereIn('followable_id', $followable_ids['User'])->get();

			$users_followed = array_map(function($elem) { return $elem['followable_id']; }, $r->toArray());
		}

		if (isset($followable_ids['Designer']))
		{
			$r = $this->eloquentFollow
				->select('followable_id')
				->where('user_id', $user_id)
				->where('followable_type', 'Designer')
				->whereIn('followable_id', $followable_ids['Designer'])->get();

			$designers_followed = array_map(function($elem) { return $elem['followable_id']; }, $r->toArray());
		}

		foreach ($followables as & $followable)
		{
			if (isset($followable['username']))
				$followable['is_followed'] = in_array($followable['id'], $users_followed);
			else
				$followable['is_followed'] = in_array($followable['id'], $designers_followed);
		}
	}

	public function getOwnerRlacFn($id, $user_id)
	{
		return function() use ($id, $user_id) {
			$r = $this->eloquentFollow->where('user_id', $user_id)
				->where('id', $id)
				->select('id')
				->first();

			if (!$r)
				return false;

			return true;
		};
	}

	public function destroyFollow($id)
	{
		$this->eloquentFollow->destroy($id);
	}
}
