<?php namespace Acme\Service\Mention;

interface MentionService {

	/**
	 * @param $data
	 * @param $text
	 * @return mixed
	 */
	public function add($data, $text);
	public function remove($type, $id);
	public function getAll($userId, $page);
}
