<?php namespace Acme\Service\Mention\Hydrator;

use \Post;

class PostHydrator implements Hydrator {
	public function hydrate($id) {
		return Note::with('user')->find($id);
	}
}
