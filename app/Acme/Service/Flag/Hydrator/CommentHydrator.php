<?php namespace Acme\Service\Flag\Hydrator;

use Acme\Model\Eloquent\Comment;

class CommentHydrator implements Hydrator {
	public function hydrate($id) {
		return Comment::select(
			'id',
			'user_id',
			'text',
			'commentable_type',
			'commentable_id',
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
