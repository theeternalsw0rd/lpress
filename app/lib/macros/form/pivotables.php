<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Lang;

/* $hidden when passed is an associative array with 'name' and 'value' keys */
Form::macro('pivotables', function($model, $pivot_name, $name, $label, $hidden = array()) {
	$hidden_input = "";
	$selected = $model->$name->lists('id');
	if(count($hidden) > 0) {
		$hidden_name = $hidden['name'];
		$hidden_value = $hidden['value'];
		$hidden_column = $hidden_name . '_id';
		$hidden_input .= Form::hidden($hidden_name, $hidden_value);
		$selected = array();
		foreach($model->$name as $$name) {
			if($$name->pivot->$hidden_column == $hidden_value) {
				$selected[] = $$name->id;
			}
		}
	}
	$pivotables = $pivot_name::all()->sortBy('label');
	$options = array();
	foreach($pivotables as $pivotable) {
		$options[$pivotable->id] = $pivotable->label;
	}
	$html = Form::open();
	$html .= $hidden_input;
	$html .= Form::select_input($name, $label, $options, $selected, array('multiple' => 'multiple'));
	$html .= "<div class='submit'>";
	$html .= HTML::icon_button(Lang::get('l-press::labels.submit_button'), 'submit', array('class' => 'button'), 'fa-check');
	$html .= "</div>";
	$html .= Form::close();
	return $html;
});
