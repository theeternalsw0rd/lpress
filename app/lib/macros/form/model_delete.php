<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Lang;

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
