<?php namespace EternalSword\Lib;

use Illuminate\Support\Facades\Lang;
use Illuminate\Html\HtmlBuilder as HTML;

HTML::macro('pivot_editor', function($model, $pivot, $url) {
	$collection = $model->$pivot;
	$html = "<ul class='collection'>";
	$icon_font = new IconFont;
	$trash_icon = $icon_font->getIcon('fa-trash-o');
	$trash_title = Lang::get('l-press::labels.delete_button');
	$base_url = $url;
	foreach($collection as $item) {
		$url = $base_url . '/' . $item->id;
		$html .= "<li class='item'><span class='label'>" . $item->label . "</span>";
		$html .= "<a href='${url}/delete' class='button-icon delete' title='${trash_title}'>$trash_icon</a>";
		$html .= "</li>";
	}
	$html .= "</ul>";
	return $html;
});
