<?php namespace Acme\Model\Eloquent;

class Comment extends \Eloquent {

	protected $guarded = ['id'];

	public function user()
	{
		return $this->belongsTo('User');
	}
}
