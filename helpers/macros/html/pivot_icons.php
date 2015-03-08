<?php namespace EternalSword\Lib;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Collective\Html\HtmlBuilder as HTML;

HTML::macro('pivot_icons', function($model, $base_url) {
	$pivots = $model->getPivots();
	$icon_font = new IconFont;
	$html = "";
	foreach($pivots as $pivot => $icon_name) {
		$url = $base_url . '/' . $pivot;
		$icon = $icon_font->getIcon($icon_name);
		$title = Str::title($pivot);
		$html .= "<a href='${url}' class='button-icon ${pivot}' title='${title}'>${icon}</a>";
	}
	return $html;
});
