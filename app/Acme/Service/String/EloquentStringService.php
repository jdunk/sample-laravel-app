<?php namespace Acme\Service\String;

use \Twig_Environment;
use \Twig_SimpleFunction;

class EloquentStringService implements StringService
{
	public $eloquentTwigLoader;

	public function __construct(EloquentTwigLoader $eloquentTwigLoader)
	{
		$this->eloquentTwigLoader = $eloquentTwigLoader;
	}

	/**
	 * Get a string value for the latest revision
	 *
	 * @param $name
	 * @return mixed
	 */
	public function get($name)
	{
		$string = $this->eloquentString
			->find('name', $name)
			->select('id', 'content')
			->orderBy('id', 'DESC')
			->first();

		if (!$string)
			return null;

		return $string->content;
	}

	/**
	 * Get a string by id
	 *
	 * @param $id
	 * @return array or null
	 */
	public function getById($id)
	{
		$result = $this->eloquentString
			->find('id', $id)
			->first();

		if ($result)
			return $result->toArray();

		return null;
	}

	public function getRevisions($name)
	{
		$results = $this->eloquentString
			->find('name', $name)
			->orderBy('id', 'DESC')
			->get();

		return $results->toArray();
	}

	/**
	 * Ge a string as a parsed twig template
	 *
	 * @param $name
	 * @param null $values
	 * @return mixed|void
	 */
	public function render($name, $values = null)
	{
		$twig = new Twig_Environment($this->eloquentTwigLoader);

		// TODO: decorate...
		// Twig_SimpleFunction

		return $twig->render($name, $values);
	}


	/**
	 * Save a new revision for provided string name
	 *
	 * @param $name
	 * @param $content
	 * @return mixed
	 */
	public function save($name, $content)
	{
		$string = $this->eloquentString->newInstance([
			'name' => $name,
			'content' => $content,
			'title' => $this->parseCamelCase($name)
		]);

		$string->save();

		return $string->toArray();
	}

	/**
	 * Destroy a revision by id
	 *
	 * @param $id
	 * @return mixed
	 */
	public function destroy($id)
	{

	}

	public function parseCamelCase($str)
	{
		return preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]|[0-9]{1,}/', ' $0', $str);
	}
} 
