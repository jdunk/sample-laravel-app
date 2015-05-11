<?php

namespace Acme\Utility;

class DeepValue {
	public static function get($name, $data, $default = null) {
		$data = (array) $data;

		if (!empty($data[$name]))
			return $data[$name];

		$nameParts = self::getNameParts($name);
		foreach ($nameParts as $part) {
			$data = (array) $data;
			$data = empty($data[$part]) ? null : $data[$part];
			if (!$data)
				break;
		}

		if (!$data)
			return $default;

		return $data;
	}

	public static function getNameParts($name) {
		if (strpos($name, '[') !== false) {
			// php style
			preg_match_all("/([^\[\]]+)/i", $name, $matches, PREG_PATTERN_ORDER);
		} elseif (strpos($name, '.') !== false) {
			// dot notation
			preg_match_all("/([^\.]+)/i", $name, $matches, PREG_PATTERN_ORDER);
		} else {
			$matches[] = array($name);
		}

		return $matches[0];
	}
}
