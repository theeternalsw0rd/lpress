<?php namespace EternalSword\Lib;

use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

Form::macro('pivot_form', function($model, $pivot, $url = NULL) {
	$pivot_data = $model->$pivot;
	var_dump($pivot_data->toJSON());
	$html = Form::open(array('url' => $url));
	$label_separator = Lang::get('l-press::labels.label_separator');
	$html .= "<div class='submit'>";
	$html .= HTML::icon_button(Lang::get('l-press::labels.submit_button'), 'submit', array('class' => 'button'), 'fa-check');
	$html .= "</div>";
	$html .= Form::close();
	return $html;
});
