<?php namespace EternalSword\Lib;
	
use Collective\Html\HtmlBuilder as HTML;

HTML::macro('icon_button', function($label, $type = 'button', $attributes = array(), $icon_class = '') {
	$icon = '';
	$icon_font = new IconFont;
	$icon = $icon_font->getIcon($icon_class);
	if($icon !== '') {
		$icon = "<span class='button-icon ${icon_class}'>$icon</span>";
	}
	return "<button type='$type'" . MacroLoader::getAttributeString($attributes) . ">$icon<span class='button-label'>$label</span></button>";
});
