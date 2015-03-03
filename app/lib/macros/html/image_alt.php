<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\HTML;

HTML::macro('image_alt', function($record) {
	if(is_array($record)) {
		$alt = $record['label'];
		foreach($record['values'] as $value) {
			if($value['field']['slug'] == 'file-description') {
				$alt = $value['current_revision']['contents'];
			}
		}
	}
	else {
		$alt = $record->label;
		foreach($record->values as $value) {
			if($value->field->slug == 'file-description') {
				$alt = $value->current_revision->contents;
			}
		}
	}
	return $alt;
});
