<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Form;

Form::macro('select_input', function($name, $label, $options, $selected, $attributes = array()) {
	$error = MacroLoader::getValidationError($name);
	if(!is_array($selected)) {
		$selected = array($selected);
	}
	if(array_key_exists('multiple', $attributes)) {
		$name = $name . '[]';
	}
	$html = "
		<div class='select'>
			${error}
			<label for='${name}' class='select'>${label}</label>
			<select id='${name}' name='${name}'" . MacroLoader::getAttributeString($attributes) . ">
	";
	foreach($options as $value => $label) {
		$attributes = in_array($value, $selected) ? " selected='selected'" : "";
		$html .= "<option value='${value}'${attributes}>${label}</option>";
	}
	$html .= "</select></div>";
	return $html;
});
