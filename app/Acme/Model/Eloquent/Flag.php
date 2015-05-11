<?php namespace Acme\Model\Eloquent;

class Flag extends BaseEloquent {
	protected $guarded = ['id'];

	public function scopeWhereFlaggable($query, $flaggable_type, $flaggable_id)
	{
		return $query->whereFlaggableType($flaggable_type)->whereFlaggableId($flaggable_id);
	}

	public function flaggable()
	{
		return $this->morphTo();
	}
}
