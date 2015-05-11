<?php namespace Acme\Storage;

use \MessageBag;
use \Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Interface BaseRepository
 * @package Acme\Storage
 * @note Please return arrays using toArray(), etc where possible to
 * decouple the ORM implementation
 */
interface BaseRepository {
	public function newInstance(array $attributes = array());

	//public function paginate(QueryBuilder $query, $perPage = 0);

	//public function simplePaginate(QueryBuilder $query, $perPage = 0);

	public function create(array $attributes, $allowedFields = array());

	public function find($id, $columns = array('*'));

	public function findOrFail($id, $columns = array('*'));

	public function update($id, array $attributes, $allowedFields = array());

	public function destroy($id);

	public function softDelete($id);

	public function addError($message, $field = null);

	public function setErrors(MessageBag $errors);

	public function getErrors();
}
