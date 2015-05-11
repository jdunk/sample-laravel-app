<?php namespace Acme\Model\Eloquent;

class MediaAsset extends \Eloquent
{
	protected $guarded = ['id'];

	public function derivatives()
	{
		return $this->hasMany('Acme\Model\Eloquent\MediaAssetDerivative');
	}
}
