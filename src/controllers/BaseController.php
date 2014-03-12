<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class BaseController extends Controller {
	protected static $models_view = ".dashboard.models";

	public static function getModelForm($slug, $model_name, $id = NULL) {
		extract(self::prepareMake());
		$model_basename = explode('\\', $model_name);
		$model_basename = end($model_basename);
		if(is_null($id)) {
			$model = new $model_name();
			$title = "Create New ${model_basename}";
		}
		else {
			$model = $model_name::find($id);
			$title = "Update ${model_basename}: " . $model->label;
		}
		$pass_to_view = array(
			'view_prefix' => $view_prefix,
			'title' => $title,
			'model' => $model,
			'model_basename' => $model_basename
		);
		return View::make($view_prefix . self::$models_view . '.form', $pass_to_view);
	}

	public static function getModelIndex($slug, $model_name, $per_page) {
		extract(self::prepareMake());
		$model_basename = explode('\\', $model_name);
		$model_basename = end($model_basename);
		$collection = $model_name::paginate($per_page);
		$pass_to_view = array(
			'view_prefix' => $view_prefix,
			'title' => $model_basename . ' Management',
			'collection' => $collection,
			'new_model' => new $model_name()
		);
		return View::make($view_prefix . self::$models_view . '.index', $pass_to_view);
	}

	public static function getModels() {
		$base_class = 'EternalSword\\LPress\\BaseModel';
		$result = array();
		foreach (get_declared_classes() as $class) {
			if (is_subclass_of($class, $base_class))
				$result[] = $class;
		}
		return $result;
	}

	public static function getRoutePrefix() {
		$route_prefix = Config::get('l-press::route_prefix');
		$route_prefix = $route_prefix == '/' ? '' : $route_prefix;
		return $route_prefix;
	}

	public static function getDashboardPrefix() {
		$route_prefix = self::getRoutePrefix();
		$route_prefix = empty($route_prefix) ? '/' : $route_prefix;
		$url_prefix = rtrim('//' . DOMAIN . '/' . $route_prefix, '/');
		$dashboard_route = '+' . Config::get('l-press::dashboard_route');
		return $url_prefix . '/' . $dashboard_route;
	}

	public static function verifyTheme() {
		define('DOMAIN', Request::server('HTTP_HOST'));
		$site = NULL;
		try { 
			$site = Site::where('domain', DOMAIN)->first();
		} catch(\Exception $e) {
			$message = $e->getMessage();
			$code = $e->getCode();
			if($code == 2002) {
				echo 'Could not connect to database.';
				die();
			}
			if(substr_count($message, 'SQLSTATE[42S02]') > 0) {
				echo 'Could not find sites table in the database, '
					. 'please ensure all migrations have been run.';
				die();
			}
			echo 'An unexpected error occurred, please try again later.';
			die();
		}
		if(!$site) {
			$site = Site::where('domain', 'wildcard')->first();
		}
		if(!$site) {
			echo 'No valid site found for this domain, ' 
				. 'if this is not on purpose you may need to seed the database, '
				. 'or you have inadvertantly removed the wildcard domain site';
			die();
		}
		define('SITE', $site->id);
		define('PRODUCTION', $site->in_production == 1 ? 'compressed' : 'uncompressed');
		try {
			$theme = Theme::find($site->theme_id);
		} catch(\Exception $e) {
			$message = $e->getMessage();
			$code = $e->getCode();
			if(substr_count($message, 'SQLSTATE[42S02]') > 0) {
				echo 'Could not find themes table in the database, '
					. 'please ensure all migrations have been run.';
				die();
			}
			echo 'An unexpected error occurred, please try again later.';
			die();
		}
		define('THEME', $theme ? $theme->slug : 'default');
	}

	public static function checkSSL($type = 'all') {
		switch($type) {
			case 'dashboard': {
				$config = 'l-press::dashboard_require_ssl';
				break;
			}
			case 'login': {
				$config = 'l-press::login_require_ssl';
				break;
			}
			default: {
				$config = 'l-press::require_ssl';
			}
		}
		if(Config::get($config) && !Request::secure()) {
			if(!Config::get('l-press::ssl_is_sha2') || UserAgent::supportsSHA2()) {
				return Redirect::secure(Request::getRequestUri());
			}
			return Redirect::route('lpress-sha2');
		}
	}

	public static function getAttributeString($attributes) {
		$attribute_string = '';
		if(is_array($attributes) && count($attributes) > 0) {
			foreach($attributes as $attribute => $value) {
				$attribute_string .= " ${attribute}='${value}'";
			}
		}
		return $attribute_string;
	}

	public static function getValidationError($name) {
		$error = "";
		if(Session::has('errors')) {
			$errors = Session::get('errors');
			if($errors->has($name)) {
				$error = $errors->first($name, " <span class='error'>:message</span>");
			}
		}
		return $error;
	}

	public static function getAssetPath($attachment = FALSE) {
		$path = '';
		if($attachment) {
			$attachment_config = Config::get('l-press::attachments');
			$attachment_path_base = $attachment_config['path_base'];
			switch($attachment_path_base) {
				case 'package': {
					$path = PATH . '/';
					break;
				}
				case 'laravel': {
					$path = base_path() . '/';
					break;
				}
				default: {
					$path = $attachment_path_base . '/';
				}
			}
		}
		else {
			$theme_config = Config::get('l-press::themes');
			$theme_path_base = $theme_config['path_base'];
			switch($theme_path_base) {
				case 'package':
					$path = PATH . '/' . $theme_config['path'] . '/' . THEME . '/assets';
					break;
				case 'laravel':
					$path = base_path() . '/' . $theme_config['path'] . '/' . THEME . '/assets';
					break;
				default:
					$path = $theme_path_base . '/' . $theme_config['path'] . '/' . THEME . '/assets';
			}
		}
		return $path;
	}

	public static function prepareMake() {
		if(!defined('THEME')) {
			echo 'An unknown error occured, please try again later.';
			die();
		}
		$view_prefix = 'l-press::themes.' . THEME . '.templates';
		self::setMacros();
		return array("view_prefix" => $view_prefix, "site" => Site::find(SITE));
	}

	public static function slugsToRoute($path) {
		$route = new \stdClass;
		$route->throw404 = FALSE;
		$route->json = FALSE;
		$route_prefix = self::getRoutePrefix();
		if(!empty($route_prefix)) {
			$real_path = explode(self::getRoutePrefix(), $path);
			$real_path = $real_path[1];
		}
		else {
			$real_path = $path;
		}
		$segments = preg_split('@/@', $real_path, NULL, PREG_SPLIT_NO_EMPTY);
		$slugs = array();
		$slug_types = array();
		$last_index = count($segments) - 1;
		$last_slug = $segments[$last_index];
		$last_slug = explode('.', $last_slug);
		if(count($last_slug) > 1 && $last_slug[1] == 'json') {
			$segments[$last_index] = $last_slug[0];
			$route->json = TRUE;
		}
		if($last_index > 0) {
			$base_index = $last_index - 1;
			// process base path first since last item has more complex logic
			for($i=$base_index;$i>=0;$i--) {
				$slug = $segments[$i];
				$record_type = RecordType::where('slug', '=', $slug)->first();
				if(count($record_type) === 0) {
					$route->throw404 = TRUE;
					break;
				}
				$parent = RecordType::find($record_type->parent_id);
				if($i > 0) {
					if($parent->depth > 0 && $parent->slug != $segments[$i-1]) {
						$route->throw404 = TRUE;
					}
				}
				else {
					$route->root_record_type = $parent;
				}
				if($i == $base_index) {
					$base_type = $record_type;
				}
				array_push($slug_types, 'record_type');
				array_push($slugs, $slug);
			}
			if(!$route->throw404) {
				$found = FALSE;
				$slug = $segments[$last_index];
				$record_type = RecordType::where('slug', '=', $slug)
				->where('parent_id', '=', $base_type->id)
				->first();
				if(count($record_type) > 0) {
					array_unshift($slug_types, 'record_type');
					array_unshift($slugs, $slug);
					$route->record_type = $record_type;
					$found = TRUE;
				}
				if(!$found) {
					$record = Record::where('site_id', '=', SITE)
					->where('record_type_id', '=', $base_type->id)
					->where('slug', '=', $slug)
					->first();
					if(count($record) > 0) {
						array_unshift($slug_types, 'record');
						array_unshift($slugs, $slug);
						$found = TRUE;
						$route->record = $record;
					}
				}
				if(!$found) {
					$symlink = Symlink::where('site_id', '=', SITE)
					->where('record_type_id', '=', $base_type->id)
					->where('slug', '=', $slug)
					->first();
					if(count($symlink) > 0) {
						array_unshift($slug_types, 'record');
						array_unshift($slugs, $slug);
						$found = TRUE;
						$route->record = $symlink->record;
					}
				}
				if($found) {
					$route->slug_types = $slug_types;
					$route->slugs = $slugs;
				}
				else {
					$route->throw404 = TRUE;
				}
			}
		}
		else {
			$found = FALSE;
			$slug = $segments[$last_index];
			$record_type = RecordType::where('slug', '=', $slug)
			->where('depth', '=', 1)
			->first();
			if(count($record_type) > 0) {
				array_push($slug_types, 'record_type');
				array_push($slugs, $slug);
				$route->record_type = $record_type;
				$found = TRUE;
			}
			if($found) {
				$route->slug_types = $slug_types;
				$route->slugs = $slugs;
			}
			else {
				$route->throw404 = TRUE;
			}
		}
		return $route;
	}

	public static function getBoolInput($key) {
		return Input::has($key) ? Input::get($key) : NULL;
	}

	public static function setMacros() {
		Form::macro('text_input', function($type, $name, $label, $value, $attributes = array()) {
			$error = self::getValidationError($name);
			$attribute_string = self::getAttributeString($attributes);
			$value = Input::old($name, $value);
			if($type == 'textarea') {
				$size = "";
				if(!array_key_exists('cols', $attributes)) {
					$size .= " cols='50' ";
				}
				if(!array_key_exists('rows', $attributes)) {
					$size .= " rows='10' ";
				}
				$input = "<textarea id='${name}' name='${name}' ${size} ${attribute_string}>${value}</textarea>";
			}
			else {
				$input = "<input id='${name}' name='${name}' type='${type}' value='${value}' ${attribute_string} />";
			}
			return "<div class='text'><label for='${name}'>${label}${error}</label>$input</div>";
		});
		Form::macro('checkbox_input', function($name, $label, $attributes = array()) {
			return "
				<div class='checkbox'>
					<label for='${name}' class='checkbox'>
						<input id='${name}' name='${name}' class='checkbox' type='checkbox'" . self::getAttributeString($attributes) . " />
						<span unselectable='on' class='checkbox-label' data-for='${name}'>${label}</span>
					</label>
				</div>
			";
		});
		Form::macro('select_input', function($name, $label, $options, $selected, $attributes = array()) {
			$html = "
				<div class='select'>
					<label for='${name}' class='select'>${label}</label>
					<select id='${name}' name='${name}'" . self::getAttributeString($attributes) . ">
			";
			foreach($options as $value => $label) {
				$attributes = $selected == $value ? " selected='selected'" : "";
				$html .= "<option value='${value}'${attributes}>${label}</option>";
			}
			$html .= "</select></div>";
			return $html;
		});
		Form::macro('file_input', function($slug, $upload_command = 'create', $single = TRUE, $value = '', $attributes = array()) {
			$type = RecordType::where('slug', '=', $slug)->first();
			if(count($type) === 0) {
				return "<div class='error'>Could not find RecordType ${slug} for file input.</div>";
			}
			$label = "Select {$type->label}";
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
				return "<div class='error'>RecordType ${slug} is not valid for file input.</div>";
			}
			$dashboard_prefix = self::getDashboardPrefix();
			$prefix = self::getRoutePrefix();
			$url_path = $prefix . '/' . $url_path;
			$url = $dashboard_prefix . "/upload?path=${file_path}/&uri=${url_path}/&upload_command=${upload_command}";
			$attribute_string = self::getAttributeString($attributes);
			$token = csrf_token();
			$class = $single ? 'single file' : 'multiple file';
			$data = "data-token='${token}' data-attachment_type='${attachment_type}' data-prefix='${dashboard_prefix}' data-path='${url_path}' data-url='${url}'";
			$hidden_name = $attributes['data-target_id'];
			$value = Input::old($hidden_name, $value);
			$hidden = "<input id='${hidden_name}' name='${hidden_name}' type='hidden' value='${value}' />";
			return "<div class='file'><a href='#${slug}' title='${label}'  class='${class}' ${data} ${attribute_string}>${label}</a>${hidden}</div>";
		});
		Form::macro('icon_button', function($label, $type = 'button', $attributes = array(), $icon_class = '') {
			$icon = '';
			$icon_font = new IconFont;
			$icon = $icon_font->getIcon($icon_class);
			if($icon !== '') {
				$icon = "<span class='button-icon ${icon_class}'>$icon</span>";
			}
			return "<button type='$type'>$icon<span class='button-label'>$label</span></button>";
		});
		HTML::macro('asset', function($type, $path, $attributes = array()) {
			$asset_domain = Config::get('l-press::asset_domain');
			$asset_domain = empty($asset_domain) ? DOMAIN : $asset_domain;
			$route_prefix = self::getRoutePrefix();
			$open = '';
			$close = '';
			switch($type) {
				case 'css': {
					$path = "css/" . PRODUCTION . '/' . $path;
					$open .= "<link rel='stylesheet' type='text/css' href='//" . $asset_domain . $route_prefix ."/+assets/" . $path . "?v";
					$close .= "'>";
					break;
				}
				case 'js': {
					$path = "js/" . PRODUCTION . '/' . $path;
					$open .= "<script type='text/javascript' src='//" . $asset_domain . $route_prefix . "/+assets/" . $path . "?v";
					$close .= "'></script>";
					break;
				}
				case 'img': {
					$open .= "<img src='//" . $asset_domain . $route_prefix . "/+assets" . $path . "?v";
					$close .= "'" . self::getAttributeString($attributes) . "/>";
					break;
				}
				default: {
					break;
				}
			}
			$version = '';
			$version = @filemtime(self::getAssetPath() . '/' . $path);
			if($version == '') {
				$close = "' data-err='$path could not be found" . $close;
			}
			return $open . $version . $close;
		});
		Form::macro('model_form', function($model, $url = NULL) {
			if(is_null($url)) {
				$url = self::getDashboardPrefix() . '/' . $model->getTable() . '/create';
			}
			$html = Form::open(array('url' => $url));
			$columns = $model->getColumns();
			$rules = $model->getRules();
			$special = $model->getSpecialInputs();
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
								$attachment_html = Form::file_input($type[1], 'create', FALSE, $value, array('data-target_id' => $property));
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
						$namespace = 'EternalSword\\LPress\\';
						$label = str_replace('_', ' ', Str::title($related_property));
						$class = $namespace.str_replace(' ', '', $label);
						if(class_exists($class) && is_subclass_of($class, $namespace.'BaseModel')) {
							$items = $class::all();
							if(in_array('required', $rule)) {
								$options_list = array();
							}
							else {
								$options_list = array('Unassigned', 'NULL');
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
				$label .= ':';
				switch($type) {
					case 'attachment': {
						$html .= $attachment_html;
						break;
					}
					case 'selection': {
						$html .= Form::select_input($property, $label, $options_list, $value, array());
						break;
					}
					case 'boolean': {
						$attributes = array();
						if($value) {
							$attributes['checked'] = 'checked';
						}
						$html .= Form::checkbox_input($property, $label, $attributes);
						break;
					}
					default: {
						if(!isset($text_type)) {
							$text_type = 'text';
						}
						$html .= Form::text_input($text_type, $property, $label, $value, array());
					}
				}
			}
			$html .= "<div class='submit'>" . Form::icon_button('OK', 'submit', array('class' => 'button'), 'fa-check') . "</div>";
			$html .= Form::close();
			return $html;
		});
		Form::macro('new_model_link', function($model) {
			$dashboard_prefix = self::getDashboardPrefix();
			$url = $dashboard_prefix . '/' . $model->getTable() . '/create';
			$model_label = $model->label;
			return "<a href='${url}' class='create model' data-model='" . get_class($model) . "'>+Add New ${model_label}</a>";
		});
		HTML::macro('collection_editor', function($collection) {
			$rows = array();
			$html = "<ul class='tabular'>";
			$dashboard_prefix = self::getDashboardPrefix();
			$icon_font = new IconFont;
			$icon = $icon_font->getIcon('fa-caret-square-o-down');
			foreach($collection as $model) {
				$url = $dashboard_prefix . '/' . $model->getTable() . '/' . $model->id;
				$html .= "<li class='item inactive'><a href='${url}' class='label'>" . $model->label . "<span class='icon'>$icon</span></a>";
				$html .= Form::model_form($model, $url);
				$html .= "</li>";
			}
			$html .= "</ul>";
			return $html;
		});
		HTML::macro('url', function($url, $text = null, $attributes = array()) {
			$attribute_string = '';
			$has_title = FALSE;
			if(is_array($attributes) && count($attributes) > 0) {
				foreach($attributes as $attribute => $value) {
					if($attribute == 'title') {
						$title = $value;
						$has_title = TRUE;
					}
					else {
						$attribute_string .= " ${attribute}='${value}'";
					}
				}
			}
			$text = is_null($text) ? $url : $text;
			$title = $has_title ? $title : $text;
			return "<a href='${url}' title='${title}'${attribute_string}>${title}</a>";
		});
		HTML::macro('imageAlt', function($record) {
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
		Blade::extend(function($value) {
			return preg_replace('/\{\$(.+)\$\}/', '<?php ${1} ?>', $value);
		});
	}
}
