<?php namespace Acme\Service\Comment;

use Acme\Validation\Validators\CommentCreate as CommentCreateValidator;
use Acme\Service\Comment\CommentService;
use Acme\Model\Eloquent\Comment;

class EloquentCommentService implements CommentService {

	protected $creationValidator;
	protected $eloquentComment;

	public function __construct(CommentCreateValidator $creationValidator, Comment $eloquentComment)
	{
		$this->creationValidator = $creationValidator;
		$this->eloquentComment = $eloquentComment;
	}

	public function createComment(array $attrs)
	{
		$this->creationValidator->validate($attrs);

		$comment = $this->eloquentComment->create(
			$attrs,
			['user_id', 'text', 'commentable_type', 'commentable_id']
		);

		return $this->getCommentWithUser($comment->id);
	}

	public function getCommentWithUser($id)
	{
		$comment = $this->eloquentComment->with([
			'user' => function($q) {
				$q->select('id', 'username', 'name', 'image_small', 'image_large', 'is_active');
			}])
			->select('id', 'text', 'created_at', 'user_id')
			->find($id)->toArray();

		unset($comment['user_id']);

		return $comment;
	}

	public function getCommentsWithUser()
	{
		$comments = $this->eloquentComment->with([
			'user' => function($q) {
				$q->select('id', 'username', 'name', 'image_small', 'image_large', 'is_active');
			}])
			->select('id', 'text', 'created_at', 'user_id')
			->get()->toArray();

		foreach ($comments as &$comment)
			unset($comment['user_id']);

		return $comments;
	}

	public function getOwnerRlacFn($id, $userId)
	{
		return function() use ($id, $userId) {
			$c = $this->eloquentComment->where('user_id', $userId)
				->where('id', $id)
				->select('id')
				->first();

			if (!$c)
				return false;

			return true;
		};
	}

	public function destroyComment($id)
	{
		$this->eloquentComment->destroy($id);
	}
}
