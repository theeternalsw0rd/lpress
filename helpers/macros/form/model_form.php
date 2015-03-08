<?php namespace EternalSword\Lib;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Collective\Html\FormBuilder as Form;
use Collective\Html\HtmlBuilder as HTML;

Form::macro('model_form', function($model, $url = NULL) {
	if(is_null($url)) {
		$dashboard_prefix = (new PrefixGenerator('dashboard'))->getPrefix();
		$url = $dashboard_prefix . '/' . $model->getTable() . '/create';
	}
	if(strpos($url, 'create') !== false) {
		if(property_exists($model, 'password_for')) {
			$password_for = $model->password_for;
		}
	}
	$html = Form::open(array('url' => $url));
	$columns = $model->getColumns();
	$rules = $model->getRules();
	$special = $model->getSpecialInputs();
	$label_separator = Lang::get('l-press::labels.label_separator');
	foreach($columns as $column) {
		$property = $column['name'];
		$label = $column['label'];
		$type = $column['type'];
		$value = $model->$property;
		if(array_key_exists($property, $special)) {
			$type = explode(':', $special[$property]);
			if(count($type) > 0) {
				switch($type[0]) {
					case 'attachment': {
						$attachment_html = Form::file_input($type[1], 'create', false, $value, array('data-target_id' => $property));
						break;
					}
					default: {
						$text_type = $type[1];
					}
				}
			}
			$type = $type[0];
		}
		else {
			if(array_key_exists($property, $rules)) {
				$rule = explode('|', $rules[$property]);
			}
			else {
				$rule = array();
			}
			if(Str::endsWith($property, '_id')) {
				$related_property = substr($property, 0, -3);
				$namespace = __NAMESPACE__ . '\\';
				$label = Lang::get("l-press::labels.${related_property}", array(), 'en');
				$class = $namespace . str_replace(' ', '', $label);
				if(class_exists($class) && is_subclass_of($class, $namespace.'BaseModel')) {
					$items = $class::all();
					if(in_array('required', $rule)) {
						$options_list = array();
					}
					else {
						$options_list = array(Lang::get('l-press::labels.null_option'), 'NULL');
					}
					$type = 'selection';
					foreach($items as $item) {
						$options_list[$item->id] = $item->label;
					}
					if(is_null($value)) {
						reset($options_list);
						$value = key($options_list);
					}
				}
			}
		}
		switch($type) {
			case 'attachment': {
				$html .= $attachment_html;
				break;
			}
			case 'selection': {
				$label .= $label_separator;
				$html .= Form::select_input($property, $label, $options_list, $value, array());
				break;
			}
			case 'boolean': {
				$attributes = array();
				$html .= Form::checkbox_input($property, $label, $value, $attributes);
				break;
			}
			default: {
				$label .= $label_separator;
				if(!isset($text_type)) {
					$text_type = 'text';
				}
				$html .= Form::text_input($text_type, $property, $label, $value, array());
				unset($text_type);
			}
		}
		if(isset($password_for) && $property == $password_for) {
			$label = Lang::get('l-press::labels.password') . $label_separator;
			$html .= Form::text_input('password', 'password', $label, '', array());
			$label = Lang::get('l-press::labels.verify_password') . $label_separator;
			$html .= Form::text_input('password', 'verify_password', $label, '', array());
		}
	}
	$html .= "<div class='submit'>";
	$html .= HTML::icon_button(Lang::get('l-press::labels.submit_button'), 'submit', array('class' => 'button'), 'fa-check');
	$html .= "</div>";
	$html .= Form::close();
	return $html;
});
