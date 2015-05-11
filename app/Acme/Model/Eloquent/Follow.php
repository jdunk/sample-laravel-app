<?php namespace Acme\Model\Eloquent;

class Follow extends \Eloquent {

	protected $guarded = ['id'];

	public function user()
	{
		return $this->belongsTo('User');
	}
}
