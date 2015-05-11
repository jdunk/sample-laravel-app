<?php
namespace Acme\Utility;

class CurlyTemplate {
	public $template = '';

	public function __construct($template) {
		$this->template = $template;
	}

	public function render($values = array()) {
		$values = (array) $values;

		$result = $this->template;

		preg_match_all("/\{\{\s*([^\}]+?)\s*\}\}/im", $this->template, $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			//$v = (!empty($values[$match[1]])) ? $values[$match[1]] : '';
			$v = DeepValue::get($match[1], $values, '');
			
			$result = str_replace($match[0], $v, $result);
		}

		return stripslashes($result);
	}
}
