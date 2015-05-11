<?php namespace Acme\Storage\Flag;

use Acme\Model\Eloquent\FlaggedItem;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentFlaggedItemRepository 
extends EloquentBaseRepository
implements FlaggedItemRepository {

	public function __construct(FlaggedItem $model)
	{
		$this->model = $model;
	}
} 
