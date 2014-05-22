<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Lang;

HTML::macro('collection_editor', function($collection, $type = 'standard') {
	$rows = array();
	$html = "<ul class='collection'>";
	$dashboard_prefix = (new PrefixGenerator('dashboard'))->getPrefix();
	$icon_font = new IconFont;
	$trash_icon = $icon_font->getIcon('fa-trash-o');
	$trash_title = Lang::get('l-press::labels.delete_button');
	$edit_icon = $icon_font->getIcon('fa-pencil-square-o');
	$edit_title = Lang::get('l-press::labels.update_button');
	$restore_icon = $icon_font->getIcon('fa-undo');
	$restore_title = Lang::get('l-press::labels.restore_button');
	foreach($collection as $model) {
		$url = $dashboard_prefix . '/' . $model->getTable() . '/' . $model->id;
		$html .= "<li class='item'><span class='label'>" . $model->label . "</span>";
		switch($type) {
			case 'trash': {
				$html .= "<a href='${url}/delete?type=force' class='button-icon force delete' title='${trash_title}'>$trash_icon</a>";
				$html .= "<a href='${url}/restore' class='button-icon restore' title='${restore_title}'>$restore_icon</a>";
				$html .= HTML::pivot_icons($model, $url);
				break;
			}
			default: {
				$html .= "<a href='${url}/delete' class='button-icon delete' title='${trash_title}'>$trash_icon</a>";
				$html .= "<a href='${url}' class='button-icon' title='${edit_title}'>$edit_icon</a>";
				$html .= HTML::pivot_icons($model, $url);
			}
		}
		$html .= "</li>";
	}
	$html .= "</ul>";
	return $html;
});
