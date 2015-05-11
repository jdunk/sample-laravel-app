<?php namespace Acme\Model\Eloquent;

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Contest extends \Eloquent {

	use SoftDeletingTrait;

	protected $guarded = ['id'];

	public function contest_region()
	{
		return $this->belongsTo('\Acme\Model\Eloquent\ContestRegion');
	}
}
