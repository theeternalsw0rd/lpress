<?php namespace EternalSword\Lib;

use Illuminate\Support\Facades\Lang;
use Illuminate\Html\HtmlBuilder as HTML;

HTML::macro('trash_bin_link', function($model) {
	$dashboard_prefix = (new PrefixGenerator('dashboard'))->getPrefix();
	$url = $dashboard_prefix . '/' . $model->getTable() . '/trash';
	$label = Lang::get('l-press::labels.trash');
	return "<a href='${url}' class='trash model' data-model='" . get_class($model) . "'>${label}</a>";
});
