<?php namespace EternalSword\LPress;

class CustomValidator extends \Illuminate\Validation\Validator {

	protected $implicitRules = array(
		'Required',
		'RequiredWith',
		'RequiredWithout',
		'RequiredIf',
		'Accepted',
		'Bool',
		'RecordExists'
	);

	public static function getOwnMessages() {
		return array(
			'bool' => 'The :attribute field must be a boolean value.',
			'record' => 'The :attribute field must exist as a record.'
		);
	}

	public function validateBool($attribute, $value, $parameters) {
		return is_bool($value) || is_null($value);
	}

	public function validateRecordExists($attribute, $value, $parameters) {
		if(!empty($parameters)) {
		}
	}
}
