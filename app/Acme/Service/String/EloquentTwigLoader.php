<?php namespace Acme\Service\String;

use Acme\Model\Eloquent\String as EloquentString;

use \Twig_Error_Loader;
use \Twig_LoaderInterface;


class EloquentTwigLoader implements Twig_LoaderInterface
{
	protected $eloquentString;

	public function __construct(EloquentString $eloquentString)
	{
		$this->eloquentString = $eloquentString;
	}

	/**
	 * Returns the most recent version of string
	 *
	 * @param string $name
	 * @return string
	 * @throws Twig_Error_Loader
	 */
	public function getSource($name)
	{
		$string = $this->eloquentString
			->find('name', $name)
			->select('id', 'content')
			->orderBy('id', 'DESC')
			->first();

		$source = null;

		if ($string && $string->content) {
			$source = $string->content;
		}

		$decoratorClassname = 'Acme\\Service\\String\\Decorators\\' . $name;
		if (class_exists($decoratorClassname, true)) {
			$decorator = new $decoratorClassname();
			if (!$source)
				$source = $decorator->defaultContent();
		}

		if (!$source) {
			// throw new Twig_Error_Loader(sprintf('Template "%s" does not exist.', $name));
		}

		return $source;
	}

	public function exists($name)
	{
		$string = $this->eloquentString
			->find('name', $name)
			->select('id')
			->first();

		if (!$string)
			return false;

		return true;
	}

	public function getCacheKey($name)
	{
		return $name;
	}

	public function isFresh($name, $time)
	{
		return true;
	}
}
