<?php namespace Acme\Storage\Contest;

use Acme\Model\Eloquent\ContestDesigner;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentContestDesignerRepository 
extends EloquentBaseRepository
implements ContestDesignerRepository {

	public function __construct(ContestDesigner $model)
	{
		$this->model = $model;
	}
} 
