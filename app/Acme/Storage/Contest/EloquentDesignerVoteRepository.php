<?php namespace Acme\Storage\Contest;

use Acme\Model\Eloquent\DesignerVote;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentDesignerVoteRepository 
extends EloquentBaseRepository
implements DesignerVoteRepository {

	public function __construct(DesignerVote $model)
	{
		$this->model = $model;
	}
} 
