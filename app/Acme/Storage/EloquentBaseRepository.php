<?php namespace Acme\Storage;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

use \Config;
use \MessageBag;

/**
 * Class EloquentBaseRepository
 * @package Acme\Storage
 */
abstract class EloquentBaseRepository implements BaseRepository
{
	protected $model;
	protected $errors;

	public function setModel(Model $model)
	{
		$this->model = $model;
	}

	public function paginate(QueryBuilder $query, $perPage = 0)
	{
		$perPage = $perPage ?: 15;
		return $query->paginate($perPage)->toArray();
	}

	public function simplePaginate(QueryBuilder $query, $perPage = 0)
	{
		$perPage = $perPage ?: 15;
		return $query->simplePaginate($perPage)->toArray();
	}

	public function newInstance(array $attributes = array())
	{
		return $this->model->newInstance($attributes);
	}

	public function all($columns = array('*'))
	{
		return $this->model->all($columns)->toArray();
	}

	public function create(array $attributes, $allowedFields = array())
	{
		if (!empty($allowedFields) && is_array($allowedFields))
			$attributes = array_only($attributes, $allowedFields);

		return $this->model->create($attributes)->toArray();
	}

	public function find($id, $columns = array('*'))
	{
		$result = $this->model->find($id, $columns);

		if (!$result)
			return null;

		return $result->toArray();
	}

	/**
	 * @param $id
	 * @param array $columns
	 * @return mixed
	 * @throws ModelNotFoundException
	 */
	public function findOrFail($id, $columns = array('*'))
	{
		$result = $this->model->find($id, $columns);

		if (!$result)
			throw new ModelNotFoundException();

		return $result->toArray();
	}

	public function update($id, array $attributes, $allowedFields = array())
	{
		$result = $this->model->find($id);

		if (!$result)
			throw new ModelNotFoundException();

		if (!empty($allowedFields) && is_array($allowedFields))
			$attributes = array_only($attributes, $allowedFields);

		foreach ($attributes as $k => $v) {
			$result->$k = $v;
		}

		return $result->save();
	}

	public function destroy($id)
	{
		return $this->model->destroy($id);
	}

	public function softDelete($id)
	{
		return $this->model->find($id)->delete();
	}

	public function addError($message, $field = null) {
		if (!$this->errors)
			$this->errors = new MessageBag();

		if (!$field)
			$field = 'error';

		$this->errors->add($field, $message);

		return $this->errors;
	}

	public function setErrors(MessageBag $errors) {
		$this->errors = $errors;
	}

	public function getErrors() {
		return $this->errors;
	}
}
