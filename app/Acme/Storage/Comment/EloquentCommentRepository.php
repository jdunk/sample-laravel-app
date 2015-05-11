<?php namespace Acme\Storage\Comment;

use Acme\Model\Eloquent\Comment;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentCommentRepository 
extends EloquentBaseRepository
implements CommentRepository {

	public function __construct(Comment $model)
	{
		$this->model = $model;
	}
} 
