<?php namespace Eternalsword\Lib;

use Illuminate\Support\Facades\Session;

class MacroLoader {
	protected $macros = array();

	public static function getAttributeString($attributes) {
		$attribute_string = '';
		if(is_array($attributes) && count($attributes) > 0) {
			foreach($attributes as $attribute => $value) {
				$attribute_string .= " ${attribute}='${value}'";
			}
		}
		return $attribute_string;
	}

	public static function getValidationError($name) {
		$error = "";
		if(Session::has('errors')) {
			$errors = Session::get('errors');
			if(!is_array($errors) && $errors->has($name)) {
				$error = $errors->first($name, " <span class='error'>:message</span>");
			}
		}
		return $error;
	}

	public function getMacros() {
		return $this->macros;
	}

	public function loadMacros($type = 'html') {
		$type = strtolower($type);
		$path = PATH . '/helpers/macros/' . $type;
		if(file_exists ($path)) {
			$files = array();
			foreach(glob($path . '/*.php') as $file) {
				require_once $file;
				$files[] = $file;
			}
			$this->macros[$type] = $files;
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
}
