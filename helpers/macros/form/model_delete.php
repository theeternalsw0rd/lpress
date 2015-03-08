<?php namespace EternalSword\Lib;

use Illuminate\Support\Facades\Lang;
use Collective\Html\FormBuilder as Form;
use Collective\Html\HtmlBuilder as HTML;

Form::macro('model_delete', function($url) {
	$html = Form::open(array('url' => $url));
	$html .= HTML::icon_button(
		Lang::get('l-press::labels.delete_button'),
		'submit',
		array(
			'class' => 'button',
			'name' => '_method',
			'value' => 'DELETE'
		),
		'fa-trash-o'
	);
	$html .= Form::close();
});
