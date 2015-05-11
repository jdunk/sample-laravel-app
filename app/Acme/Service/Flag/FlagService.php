<?php namespace Acme\Service\Flag;

interface FlagService {

	public function createFlag(array $attrs);
	public function updateFlaggedItem($id, array $attrs);
	public function getFlaggedItem($flagged_item_id);
	public function getFlaggedItems();
}
