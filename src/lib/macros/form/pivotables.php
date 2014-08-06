<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Lang;

Form::macro('pivotables', function($model, $pivot_name, $name, $label) {
	$selected = $model->$name->lists('id');
	$pivotables = $pivot_name::all()->sortBy('label');
	$options = array();
	foreach($pivotables as $pivotable) {
		$options[$pivotable->id] = $pivotable->label;
	}
	$html = Form::open();
	$html .= Form::select_input($name, $label, $options, $selected, array('multiple' => 'multiple'));
	$html .= "<div class='submit'>";
	$html .= HTML::icon_button(Lang::get('l-press::labels.submit_button'), 'submit', array('class' => 'button'), 'fa-check');
	$html .= "</div>";
	$html .= Form::close();
	return $html;
});
