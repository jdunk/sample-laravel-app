<?php namespace Acme\Service\Mention;

class MentionHydratorFactoryException extends \Exception {}

class MentionHydratorFactory {
	public static $classRoot = 'Acme\\Service\\Mention\\Hydrator';

	public static function make($type) {
		$className = $type . 'Hydrator';
		$class = self::$classRoot . '\\' . $className;
		if (!class_exists($class)) 
			throw new MentionHydratorFactoryException('No hydrator for ' . $className);
		
		return new $class();
	}
}
