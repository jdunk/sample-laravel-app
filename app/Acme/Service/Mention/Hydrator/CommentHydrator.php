<?php namespace Acme\Service\Mention\Hydrator;

use \Comment;

class CommentHydrator implements Hydrator {
	public function hydrate($id) {
		return Comment::find($id);
	}
}
