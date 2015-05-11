<?php namespace Acme\Storage\Designer;

use Acme\Model\Eloquent\Designer;
use Acme\Storage\BaseRepository;
use Acme\Storage\EloquentBaseRepository;

class EloquentDesignerRepository 
extends EloquentBaseRepository
implements DesignerRepository {

	public function __construct(Designer $model)
	{
		$this->model = $model;
	}
} 
