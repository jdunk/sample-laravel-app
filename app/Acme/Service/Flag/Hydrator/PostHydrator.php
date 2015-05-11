<?php namespace Acme\Service\Flag\Hydrator;

use Acme\Model\Eloquent\Post;

class PostHydrator implements Hydrator {
	public function hydrate($id) {
		return Post::select(
			'id',
			'user_id',
			'created_at'
		)
		->with(['user' => function($q) {
			$q->select(
				'id',
				'username',
				'name',
				'email',
				'image_small',
				'image_large',
				'is_active',
				'created_at'
			);
		}])->find($id);
	}
}
