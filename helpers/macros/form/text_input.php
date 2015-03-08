<?php namespace EternalSword\Lib;

use Illuminate\Support\Facades\Input;
use Collective\Html\FormBuilder as Form;

Form::macro('text_input', function($type, $name, $label, $value, $attributes = array()) {
	$error = MacroLoader::getValidationError($name);
	$attribute_string = MacroLoader::getAttributeString($attributes);
	$value = Input::old($name, $value);
	if($type == 'textarea') {
		$size = "";
		if(!array_key_exists('cols', $attributes)) {
			$size .= " cols='50' ";
		}
		if(!array_key_exists('rows', $attributes)) {
			$size .= " rows='10' ";
		}
		$input = "<textarea id='${name}' name='${name}' ${size} ${attribute_string}>${value}</textarea>";
	}
	else {
		$input = "<input id='${name}' name='${name}' type='${type}' value='${value}' ${attribute_string} />";
	}
	return "<div class='text'><label for='${name}'>${label}${error}</label>$input</div>";
});
