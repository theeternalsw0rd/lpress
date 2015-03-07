<?php namespace EternalSword\Lib;

use Illuminate\Support\Facades\Lang;
use Illuminate\Html\HtmlBuilder as HTML;

HTML::macro('new_model_link', function($model) {
	$dashboard_prefix = (new PrefixGenerator('dashboard'))->getPrefix();
	$url = $dashboard_prefix . '/' . $model->getTable() . '/create';
	$label = Lang::get('l-press::labels.new_model');
	return "<a href='${url}' class='create model' data-model='" . get_class($model) . "'>${label}</a>";
});
