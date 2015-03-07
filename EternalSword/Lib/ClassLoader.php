<?php namespace EternalSword\Lib;

class ClassLoader {
	// returns namespaced classes autoloaded by composer
	public static function getClasses($autoload_path, $namespace) {
		$loader = require $autoload_path;
		return array_filter(array_keys($loader->getClassMap()), function($value) use ($namespace) {
			return strpos($value, $namespace) !== false;
		});
	}
}
