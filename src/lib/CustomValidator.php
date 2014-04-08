<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Lang;

class CustomValidator extends \Illuminate\Validation\Validator {

	protected $implicitRules = array(
		'Required',
		'RequiredWith',
		'RequiredWithout',
		'RequiredIf',
		'Accepted',
		'Bool',
		'RecordExists',
		'Same'
	);

	protected function doReplacements($message, $attribute, $rule, $parameters) {
		$message = str_replace(':attribute', "<span class='label'>" . $this->getAttribute($attribute) . "</span>", $message);
		if (isset($this->replacers[snake_case($rule)])) {
			$message = $this->callReplacer($message, $attribute, snake_case($rule), $parameters);
		}
		elseif (method_exists($this, $replacer = "replace{$rule}")) {
			$message = $this->$replacer($message, $attribute, $rule, $parameters);
		}
		return $message;
	}

	public static function getOwnMessages() {
		return Lang::get('l-press::validation');
	}

	public function validateBool($attribute, $value, $parameters) {
		return is_bool($value) || is_null($value);
	}

	public function validateRecordExists($attribute, $value, $parameters) {
		if(empty($value)) {
			return TRUE;
		}
		if(!is_numeric($value)) {
			return FALSE;
		}
		$record = Record::find($value);
		if(count($record) !== 1) {
			return FALSE;
		}
		if(!empty($parameters)) {
			$record->load('record_type');
			$record_type_slug = $parameters[0];
			$record_type = $record->record_type;
			while($record_type->depth > 0) {
				if($record_type->slug == $record_type_slug) {
					return TRUE;
				}
				$record_type = RecordType::find($record_type->parent_id);
			}
			return FALSE;
		}
		return TRUE;
	}

	public function validateDomain($attribute, $value, $parameters) {
		if($value == 'wildcard') {
			return TRUE;
		}
		$parts = explode('.', $value);
		foreach($parts as $part) {
			if(preg_match("/^[\pL\pN][\pL\pN-][\pL\pN]+$/", $part) != 1) {
				return FALSE;
			}
		}
		if(preg_match("/[\pL]+/", $parts[count($parts) - 1]) != 1) {
			return FALSE;
		}
		return TRUE;
	}
}
