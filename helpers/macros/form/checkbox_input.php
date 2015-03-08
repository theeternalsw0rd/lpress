<?php namespace EternalSword\Lib;

use Illuminate\Support\Facades\Input;
use Collective\Html\FormBuilder as Form;

Form::macro('checkbox_input', function($name, $label, $value, $attributes = array()) {
	$error = MacroLoader::getValidationError($name);
	$value = Input::old($name, $value) ? " checked='checked'" : "";
	return "
		<div class='checkbox'>
			<label for='${name}' class='checkbox'>
				<input id='${name}' name='${name}' class='checkbox' type='checkbox'" . $value . MacroLoader::getAttributeString($attributes) . " />
				<span unselectable='on' class='checkbox-label' data-for='${name}'>${label}${error}</span>
			</label>
		</div>
	";
});
