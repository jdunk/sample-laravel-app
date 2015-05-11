<?php namespace Acme\Model\Eloquent;

class InstagramAccount extends \Eloquent {
	protected $guarded = ['id'];

	public function user() {
		return $this->belongsTo('Acme\Model\Eloquent\User');
	}
}
