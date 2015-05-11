<?php namespace Acme\Service\Follow;

interface FollowService {

	public function createFollow(array $attrs);
	public function getFollow($follow_id);
	public function getFollows($user_id, $only_hushed=false);
	public function getOwnerRlacFn($id, $user_id);
	public function destroyFollow($id);
}
