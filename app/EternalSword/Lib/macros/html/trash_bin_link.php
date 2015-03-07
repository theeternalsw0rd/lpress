<?php namespace App\EternalSword\Lib\Macros;

use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Lang;

HTML::macro('trash_bin_link', function($model) {
	$dashboard_prefix = (new PrefixGenerator('dashboard'))->getPrefix();
	$url = $dashboard_prefix . '/' . $model->getTable() . '/trash';
	$label = Lang::get('l-press::labels.trash');
	return "<a href='${url}' class='trash model' data-model='" . get_class($model) . "'>${label}</a>";
});
