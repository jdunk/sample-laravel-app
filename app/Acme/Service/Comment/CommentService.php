<?php namespace Acme\Service\Comment;

interface CommentService {

	public function createComment(array $attrs);
	public function getCommentWithUser($id);
	public function getCommentsWithUser();
	public function getOwnerRlacFn($id, $userId);
	public function destroyComment($id);
}
