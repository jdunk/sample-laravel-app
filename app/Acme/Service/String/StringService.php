<?php namespace Acme\Service\String;


interface StringService {
	/**
	 * Get a string value parsed with optional template values
	 *
	 * @param $name
	 * @return mixed
	 */
	public function get($name);

	/**
	 * Get a string by id
	 *
	 * @param $id
	 * @return array or null
	 */
	public function getById($id);

	/**
	 * Get array of revisions for named template
	 *
	 * @param $name
	 * @return mixed
	 */
	public function getRevisions($name);

	/**
	 * Render a twig template string
	 *
	 * @param $name
	 * @param null $values
	 * @return mixed
	 */
	public function render($name, $values = null);

	/**
	 * Save a new revision for provided string name
	 *
	 * @param $name
	 * @param $content
	 * @return mixed
	 */
	public function save($name, $content);

	/**
	 * Destroy a revision by id
	 *
	 * @param $id
	 * @return mixed
	 */
	public function destroy($id);
} 
