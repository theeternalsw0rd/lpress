<?php namespace EternalSword\Lib;

use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

Form::macro('file_input', function($slug, $upload_command = 'create', $single = TRUE, $value = '', $attributes = array()) {
	$type = RecordType::where('slug', '=', $slug)->first();
	if(count($type) === 0) {
		return "<div class='error'>" . Lang::get('l-press::errors.missingRecordType', array('slug' => $slug)) . "</div>";
	}
	$label = Lang::get('l-press::labels.file_select', array('type' => $type->label));
	$file_path = $slug;
	$url_path = $slug;
	while($type->depth > 1) {
		$type = $type->parent_type()->first();
		$attachment_type = $type->slug;
		$file_path = $attachment_type . '/' . $file_path;
		$url_path = $attachment_type . '/' . $url_path;
	}
	$site = Site::find(SITE);
	$root_type = $type->parent_type()->first();
	$file_path = $root_type->slug . '/' . $site->domain . '/' . $file_path;
	if ($root_type->slug != 'attachments') {
		return "<div class='error'>" . Lang::get('l-press::errors.invalidRecordType', array('slug' => $slug)) . "</div>";
	}
	$prefix_generator = new PrefixGenerator;
	$prefix = $prefix_generator->getPrefix();
	$prefix_generator->setType('dashboard');
	$dashboard_prefix = $prefix_generator->getPrefix();
	$url_path = $prefix . '/' . $url_path;
	$url = $dashboard_prefix . "/upload?path=${file_path}/&uri=${url_path}/&upload_command=${upload_command}";
	$attribute_string = MacroLoader::getAttributeString($attributes);
	$token = csrf_token();
	$class = $single ? 'single file' : 'multiple file';
	$data = "data-token='${token}' data-attachment_type='${attachment_type}' data-prefix='${dashboard_prefix}' data-path='${url_path}' data-url='${url}'";
	$hidden_name = $attributes['data-target_id'];
	$value = Input::old($hidden_name, $value);
	$hidden = "<input id='${hidden_name}' name='${hidden_name}' type='hidden' value='${value}' />";
	return "<div class='file'><a href='#${slug}' title='${label}'  class='${class} button' ${data} ${attribute_string}>${label}</a>${hidden}</div>";
});
